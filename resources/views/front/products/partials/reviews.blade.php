<!-- Reviews Section: two-column layout (main reviews + side form) -->
<div class="reviews-section enhanced">
    <div class="reviews-main">
        @if($reviews->count() > 0)
        <!-- Reviews List -->
        <div class="reviews-list">
            @foreach($reviews as $review)
            <div class="review-item">
                <div class="review-header">
                    <div class="reviewer-info">
                        <div class="reviewer-avatar">
                            @if($review->user->avatar)
                            <img src="{{ asset($review->user->avatar) }}" alt="{{ $review->user->name }}">
                            @else
                            <div class="avatar-placeholder">{{ strtoupper(substr($review->user->name, 0, 1)) }}</div>
                            @endif
                        </div>
                        <div class="reviewer-details">
                            <h4 class="reviewer-name">{{ $review->user->name }}</h4>
                            <div class="review-meta">
                                <div class="review-rating">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star star {{ $i <= $review->rating ? 'filled' : '' }}"></i>
                                    @endfor
                                </div>
                                <span class="review-date">{{ $review->created_at->format('M d, Y') }}</span>
                            </div>
                        </div>
                    </div>
                    @if($review->verified_purchase)
                    <div class="verified-badge">
                        <i class="fas fa-shield-check"></i>
                        Verified Purchase
                    </div>
                    @endif
                </div>

                <div class="review-content">
                    <p class="review-text">{{ $review->comment }}</p>

                    @if($review->images && count($review->images) > 0)
                    <div class="review-images">
                        @foreach($review->images as $image)
                        <div class="review-image">
                            <img src="{{ asset($image) }}" alt="Review image" loading="lazy">
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>

                @if($review->helpful_count > 0)
                <div class="review-actions">
                    <div class="helpful-count">
                        <i class="fas fa-thumbs-up"></i>
                        {{ $review->helpful_count }} found this helpful
                    </div>
                </div>
                @endif
            </div>
            @endforeach
        </div>

        <!-- Reviews Pagination -->
        @if($reviews instanceof \Illuminate\Pagination\LengthAwarePaginator && $reviews->hasPages())
        <div class="reviews-pagination">{{ $reviews->links() }}</div>
        @endif
        @else
        <div class="no-reviews fancy-empty">
            <div class="no-reviews-icon">
                <i class="fas fa-star"></i>
            </div>
            <h3>{{ __('No Reviews Yet') }}</h3>
            <p>{{ __('Be the first to review this product and help others make informed decisions.') }}</p>
        </div>
        @endif
    </div>

    <aside class="reviews-side">
        @auth
        @if($reviewCanSubmit)
        <div class="write-review-section card-surface">
            <h3 class="write-title">{{ __('Write a Review') }}</h3>
            <form class="review-form" id="reviewForm" action="{{ route('reviews.store', $product->id) }}" method="POST">
                @csrf
                <div class="form-group">
                    <label class="form-label">{{ __('Your Rating') }}</label>
                    <div class="rating-input" id="ratingInput">
                        @for($i = 1; $i <= 5; $i++) <button type="button" class="star-btn" data-rating="{{ $i }}"
                            aria-label="{{ __('Rate :n star(s)', ['n'=>$i]) }}">
                            <i class="fas fa-star star"></i>
                            </button>
                            @endfor
                            <input type="hidden" name="rating" id="ratingValue" required>
                    </div>
                    <fieldset class="rating-fallback">
                        <legend>{{ __('Choose rating (fallback)') }}</legend>
                        <div class="stars-inline">
                            @for($i=1;$i<=5;$i++) <input type="radio" id="rf{{ $i }}" name="rating_fallback"
                                value="{{ $i }}">
                                <label for="rf{{ $i }}">{{ $i }}</label>
                                @endfor
                        </div>
                    </fieldset>
                </div>
                <div class="form-group">
                    <label for="reviewComment" class="form-label">{{ __('Your Review') }}</label>
                    <textarea class="form-control" id="reviewComment" name="comment" rows="4"
                        placeholder="{{ __('Share your experience with this product...') }}" required></textarea>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane btn-icon"></i>
                        {{ __('Submit Review') }}
                    </button>
                </div>
            </form>
        </div>
        @else
        <div class="buyer-only-info card-surface">
            <h3>{{ __('Reviews are for verified buyers only') }}</h3>
            <p>{{ __('You can submit a review after your purchase is completed.') }}</p>
        </div>
        @endif
        @else
        <div class="login-prompt card-surface">
            <div class="login-prompt-content">
                <h3>{{ __('Want to Write a Review?') }}</h3>
                <p>{{ __('Please log in to share your experience with this product.') }}</p>
                <div class="login-actions">
                    <a href="{{ route('login') }}" class="btn btn-primary">{{ __('Log In') }}</a>
                    <a href="{{ route('register') }}" class="btn btn-outline">{{ __('Sign Up') }}</a>
                </div>
            </div>
        </div>
        @endauth
    </aside>
</div>