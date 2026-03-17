<?php

namespace App\Filament\Admin\Pages;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Payment;
use BackedEnum;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Maatwebsite\Excel\Excel;
use UnitEnum;



class Reports extends Page implements HasForms
{
    use InteractsWithForms;
    protected string $view = 'filament.admin.pages.reports';
    protected static String|BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar';
    protected static UnitEnum|string|null $navigationGroup = 'Reports';
    protected static ?int $navigationSort = 6;

    public $report_type = 'appointments';
    public $datefrom;
    public $dateto;
    public $doctor_id = null;
    public $status = null;

    public function mount()
    {
        $this->datefrom = now()->startOfMonth()->format('Y-m-d');
        $this->dateto = now()->format('Y-m-d');
    }

    public function getFormSchema(): array
    {
        return [
            Select::make('report_type')
                ->label('Report Type')
                ->options([
                    'appointments' => 'Appointments',
                    'revenue' => 'Revenue',
                    'patients' => 'Patients',
                    'doctors' => 'Doctors',
                    'cancellations' => 'Cancellations_report',
                ])
                ->required()
                ->reactive(),
            DatePicker::make('datefrom')
                ->label('Date From')
                ->maxDate(fn($get) => $get('dateto') ?: now())
                ->required(),
            DatePicker::make('dateto')
                ->label('Date To')
                ->minDate(fn($get) => $get('datefrom'))
                ->maxDate(now())
                ->required(),
            Select::make('doctor_id')
                ->label('Filter by Doctor')
                ->options(Doctor::all()->pluck('first_name', 'id'))
                ->searchable()
                ->preload(),

            Select::make('status')
                ->label('Filter by Status')
                ->options([
                    'pending' => 'Pending',
                    'confirmed' => 'Confirmed',
                    'completed' => 'Completed',
                    'cancelled' => 'Cancelled',
                ])
        ];
    }

    public function generateReport()
    {

        $data = $this->getReportData();

        return response()->streamDownload(function () use ($data) {
            echo Pdf::loadView('reports.' . $this->report_type, $data)
                ->setPaper('a4', 'landscape')
                ->output();
        }, $this->report_type . '_report_' . now()->format('Y_m_d_H_i_s') . '.pdf');
    }



    protected function getReportData(): array
    {
        $dateFrom = $this->datefrom;
        $dateTo = $this->dateto;

        return match ($this->report_type) {
            'appointments' => $this->getAppointmentsData($dateFrom, $dateTo),
            'revenue' => $this->getRevenueData($dateFrom, $dateTo),
            'doctors' => $this->getDoctorsData($dateFrom, $dateTo),
            'patients' => $this->getPatientsData($dateFrom, $dateTo),
            'cancellations' => $this->getCancellationsData($dateFrom, $dateTo),
            default => [],
        };
    }

    protected function getAppointmentsData($from, $to): array
    {
        $query = Appointment::with(['patient', 'doctor'])
            ->whereBetween('appointment_date', [$from, $to]);

        if ($this->doctor_id) {
            $query->where('doctor_id', $this->doctor_id);
        }

        if ($this->status) {
            $query->where('status', $this->status);
        }

        $appointments = $query->get();

        return [
            'appointments' => $appointments,
            'total' => $appointments->count(),
            'completed' => $appointments->where('status', 'completed')->count(),
            'cancelled' => $appointments->where('status', 'cancelled')->count(),
            'pending' => $appointments->where('status', 'pending')->count(),
            'date_from' => $from,
            'date_to' => $to,
        ];
    }

    protected function getRevenueData($from, $to): array
    {
        $query = Payment::with(['appointment.patient', 'appointment.doctor'])
            ->whereBetween('initiated_at', [$from, $to])
            ->where('status', 'completed');

        if ($this->doctor_id) {
            $query->whereHas('appointment', fn($q) => $q->where('doctor_id', $this->doctor_id));
        }

        $payments = $query->get();

        return [
            'payments' => $payments,
            'total_revenue' => $payments->sum('amount'),
            'total_transactions' => $payments->count(),
            'by_method' => $payments->groupBy('payment_method')->map->sum('amount'),
            'date_from' => $from,
            'date_to' => $to,
        ];
    }

    protected function getDoctorsData($from, $to): array
    {
        $doctors = Doctor::with(['appointments' => function ($q) use ($from, $to) {
            $q->whereBetween('appointment_date', [$from, $to]);
        }])
            ->withCount(['appointments' => function ($q) use ($from, $to) {
                $q->whereBetween('appointment_date', [$from, $to]);
            }])
            ->withAvg('reviews', 'rating')
            ->get();

        return [
            'doctors' => $doctors,
            'date_from' => $from,
            'date_to' => $to,
        ];
    }

    protected function getPatientsData($from, $to): array
    {
        $patients = Patient::whereBetween('created_at', [$from, $to])->get();

        return [
            'total_patients' => $patients->count(),
            'by_gender' => $patients->groupBy('gender')->map->count(),
            'by_blood_group' => $patients->groupBy('blood_group')->map->count(),
            'patients' => $patients,
            'date_from' => $from,
            'date_to' => $to,
        ];
    }

    protected function getCancellationsData($from, $to): array
    {
        $cancellations = Appointment::with(['patient', 'doctor'])
            ->where('status', 'cancelled')
            ->whereBetween('appointment_date', [$from, $to])
            ->get();

        return [
            'cancellations' => $cancellations,
            'total' => $cancellations->count(),
            'by_doctor' => $cancellations->groupBy('doctor_id')->map->count(),
            'date_from' => $from,
            'date_to' => $to,
        ];
    }
}
