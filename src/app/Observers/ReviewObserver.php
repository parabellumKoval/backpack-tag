<?php

namespace Backpack\Reviews\app\Observers;

use Backpack\Reviews\app\Models\Review;
use Backpack\Store\app\Models\Product;
use App\Notifications\ReviewBonus;

class ReviewObserver
{
  public function created(Review $review){
    $product = $review->product;

    $this->updateProductRating($product);
  }

// Проблема:
// При одновременном изменении рейтинга и отношения с товаром,
// рейтинг для товара  рассчитывается исходя из 
// предыдущего значения рейтинга отзыва.

  public function updated(Review $review){
    $product = $review->product;
    $productId = $product? $product->id : null;
    $originalProductId = $review->getOriginal()['product_id'];

    if($originalProductId && $originalProductId != $productId) {
      $this->updateProductRating(Product::find($originalProductId));
    }

    $this->updateProductRating($product);
    
    // Удалить из пакета
    if($review->is_moderated && $review->transaction) {
      $transaction = $review->transaction;
      
      if(!$transaction)	return;
      
      $usermeta = $transaction->usermeta;
      
      if(!$usermeta) return;
      	
      $transaction->balance = $usermeta->bonus_balance + $transaction->change;
      $transaction->is_completed = 1;
      $transaction->created_at = now();
      $transaction->save();
      
      $usermeta->notify(new ReviewBonus($transaction));
      //\Auth::user()->usermeta->notify(new ReviewBonus($transaction));
    }
  }

  public function deleted(Review $review) {
    $product = $review->product;

    $this->updateProductRating($product);
  }

  public function updateProductRating($product) {
    
    if($product == null)
      return;

    $productReviews = $product->reviews->where('is_moderated', 1)->where('rating', '!=', null);
    $averageRating = 0;

    if(!count($productReviews)) {
      $product->rating = null;
      $product->save();
      return;
    }

    foreach($productReviews as $review) {
      $averageRating += $review->rating;
    }

    $averageRating = $averageRating / count($productReviews);
    $product->rating = round($averageRating);
    $product->save();
  }
  
}
