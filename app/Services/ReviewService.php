<?php

namespace App\Services;

use App\Models\Review;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Nette\Utils\Json;

class ReviewService {

    public function createReview($data){
        $review = Review::create([
            'doctor_id' =>$data['doctor_id'],
            
        ])
    }

    public function updateReview(){}

    public function getAllReviews(){}

    public function deleteReview(){}
}
