<?php

return [
  'enable_review_type' => false,
  'enable_review_for_product' => true,
  'enable_rating' => true,

  // CATALOG
  'per_page' => 12,

  // OWNER
  'owner_model' => 'Backpack\Profile\app\Models\Profile',

  //GUARD
  'auth_guard' => 'profile',

  // Seed batabase
  'reviewable_model' => 'Backpack\Store\app\Models\Product',

  'rating_type' => 'detailed', // 'detailed' - allow multiple rating params, 'simple' - allow only single digit  

  'detailed_rating_params' => [
    'param_1' => 'label_1',
    'param_2' => 'label_2',
    'param_3' => 'label_2',
  ],

  'rating_length' => 5,

  // Override
  'review_model' => 'Backpack\Reviews\app\Models\Review' 
];
