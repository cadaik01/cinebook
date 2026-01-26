{{--
/**
 * Promotions Page
 * 
 * Display all cinema promotions and special offers including:
 * - Cinema gifts and combos
 * - Member rewards and benefits
 * - Student discounts
 * - Birthday specials
 * - Seasonal promotions
 * - Tab-based navigation for different categories
 */
--}}
@extends('layouts.main')

@section('title', 'Special Promotions - TCA Cine')

@push('styles')
<style>
    /* ==================== Promotions Page Styles ==================== */
    .promotions-page {
        max-width: 1200px;
        margin: 40px auto;
        padding: 20px;
    }

    .promotions-header {
        text-align: center;
        margin-bottom: 40px;
    }

    .promotions-header h1 {
        font-size: 2.5rem;
        color: var(--color-primary, #1a2233);
        margin-bottom: 10px;
    }

    .promotions-header p {
        font-size: 1.1rem;
        color: #666;
    }

    /* Tab Navigation */
    .promotion-tabs {
        display: flex;
        justify-content: center;
        gap: 10px;
        margin-bottom: 40px;
        border-bottom: 2px solid #e0e0e0;
        flex-wrap: wrap;
    }

    .tab-btn {
        background: none;
        border: none;
        padding: 15px 30px;
        font-size: 1.1rem;
        font-weight: 500;
        color: #666;
        cursor: pointer;
        border-bottom: 3px solid transparent;
        transition: all 0.3s;
    }

    .tab-btn:hover {
        color: var(--color-primary, #1a2233);
        background: #f8f9fa;
    }

    .tab-btn.active {
        color: var(--color-primary, #1a2233);
        border-bottom-color: var(--color-accent, #f7c873);
    }

    /* Tab Content */
    .tab-content {
        display: none;
    }

    .tab-content.active {
        display: block;
        animation: fadeIn 0.5s;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Promotion Cards */
    .promotion-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        gap: 30px;
        margin-top: 30px;
    }

    .promotion-card {
        background: linear-gradient(135deg, #fffbf0 0%, #fff 100%);
        border: 2px solid #f7c873;
        border-radius: 12px;
        padding: 25px;
        transition: all 0.3s;
        box-shadow: 0 2px 8px rgba(247, 200, 115, 0.2);
    }

    .promotion-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(247, 200, 115, 0.3);
    }

    .promotion-card h3 {
        font-size: 1.4rem;
        color: var(--color-primary, #1a2233);
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .promotion-card .icon {
        font-size: 1.8rem;
    }

    .promotion-card p {
        color: #555;
        line-height: 1.6;
        margin-bottom: 15px;
    }

    .promotion-details {
        background: #fff;
        padding: 15px;
        border-radius: 8px;
        margin: 15px 0;
    }

    .promotion-details h4 {
        color: #008080;
        margin-bottom: 10px;
    }

    .promotion-details ul {
        list-style: none;
        padding-left: 0;
    }

    .promotion-details li {
        padding: 5px 0;
        color: #555;
    }

    .promotion-details li::before {
        content: "✓ ";
        color: #008080;
        font-weight: bold;
        margin-right: 8px;
    }

    .promotion-cta {
        display: inline-block;
        background: linear-gradient(135deg, #f7c873 0%, #e6a040 100%);
        color: #1a2233;
        padding: 12px 25px;
        border-radius: 6px;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s;
        box-shadow: 0 2px 8px rgba(247, 200, 115, 0.3);
    }

    .promotion-cta:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(247, 200, 115, 0.4);
    }

    .validity {
        color: #999;
        font-size: 0.9rem;
        margin-top: 10px;
        font-style: italic;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .promotion-grid {
            grid-template-columns: 1fr;
        }

        .promotion-tabs {
            flex-direction: column;
        }

        .tab-btn {
            width: 100%;
        }
    }
</style>
@endpush

@section('content')
<div class="promotions-page">
    <div class="promotions-header">
        <h1>🎁 Special Promotions & Offers</h1>
        <p>Enjoy amazing deals and exclusive benefits at TCA Cine</p>
    </div>

    <!-- Tab Navigation -->
    <div class="promotion-tabs">
        <button class="tab-btn active" onclick="openTab(event, 'cinema-gifts')">🎟️ Cinema Gifts</button>
        <button class="tab-btn" onclick="openTab(event, 'member-rewards')">👑 Member Rewards</button>
        <button class="tab-btn" onclick="openTab(event, 'student-deals')">🎓 Student Deals</button>
        <button class="tab-btn" onclick="openTab(event, 'seasonal')">🎉 Seasonal Offers</button>
    </div>

    <!-- Cinema Gifts Tab -->
    <div id="cinema-gifts" class="tab-content active">
        <div class="promotion-grid">
            <div class="promotion-card">
                <h3><span class="icon">🍿</span> Free Premium Popcorn Combo</h3>
                <p>Get a free premium popcorn combo with every ticket purchase!</p>
                
                <div class="promotion-details">
                    <h4>What's Included:</h4>
                    <ul>
                        <li>Large Popcorn (Butter or Caramel)</li>
                        <li>2 Medium Soft Drinks</li>
                        <li>Movie Snack Coupon for next visit</li>
                    </ul>
                </div>

                <a href="{{ route('now_showing') }}" class="promotion-cta">Claim Now</a>
                <p class="validity">Valid until: March 31, 2026</p>
            </div>

            <div class="promotion-card">
                <h3><span class="icon">🎬</span> Exclusive Movie Merchandise</h3>
                <p>Buy 2 tickets and get 1 free collectible item from our exclusive merchandise collection!</p>
                
                <div class="promotion-details">
                    <h4>Available Items:</h4>
                    <ul>
                        <li>Limited Edition Movie Posters</li>
                        <li>Character Figurines</li>
                        <li>Branded T-Shirts</li>
                        <li>Movie Soundtrack CDs</li>
                    </ul>
                </div>

                <a href="{{ route('now_showing') }}" class="promotion-cta">Shop Now</a>
                <p class="validity">Limited stock available</p>
            </div>

            <div class="promotion-card">
                <h3><span class="icon">🎁</span> Weekend Combo Deal</h3>
                <p>Special combo packages for weekend movie marathons!</p>
                
                <div class="promotion-details">
                    <h4>Package Includes:</h4>
                    <ul>
                        <li>2 Movie Tickets (Any showtime)</li>
                        <li>Family Size Popcorn</li>
                        <li>4 Soft Drinks</li>
                        <li>Free Parking Pass</li>
                    </ul>
                </div>

                <a href="{{ route('now_showing') }}" class="promotion-cta">Get Deal</a>
                <p class="validity">Available: Friday - Sunday</p>
            </div>
        </div>
    </div>

    <!-- Member Rewards Tab -->
    <div id="member-rewards" class="tab-content">
        <div class="promotion-grid">
            <div class="promotion-card">
                <h3><span class="icon">🎂</span> Birthday Special</h3>
                <p>Celebrate your birthday with us! Get a free movie ticket on your special day.</p>
                
                <div class="promotion-details">
                    <h4>Birthday Benefits:</h4>
                    <ul>
                        <li>1 Free Movie Ticket (Any movie, any time)</li>
                        <li>Free Medium Popcorn & Drink</li>
                        <li>20% off on additional tickets</li>
                        <li>Priority Seat Selection</li>
                    </ul>
                </div>

                <a href="{{ route('register') }}" class="promotion-cta">Register Now</a>
                <p class="validity">Valid for 7 days around your birthday</p>
            </div>

            <div class="promotion-card">
                <h3><span class="icon">⭐</span> VIP Membership</h3>
                <p>Join our VIP program and enjoy exclusive benefits year-round!</p>
                
                <div class="promotion-details">
                    <h4>VIP Perks:</h4>
                    <ul>
                        <li>10% off all ticket purchases</li>
                        <li>Free seat upgrades (subject to availability)</li>
                        <li>Early access to new releases</li>
                        <li>Exclusive member-only screenings</li>
                        <li>Free snack vouchers monthly</li>
                    </ul>
                </div>

                <a href="{{ route('register') }}" class="promotion-cta">Join VIP</a>
                <p class="validity">Annual membership: $49.99</p>
            </div>

            <div class="promotion-card">
                <h3><span class="icon">🎯</span> Loyalty Points</h3>
                <p>Earn points with every purchase and redeem for free tickets and snacks!</p>
                
                <div class="promotion-details">
                    <h4>How It Works:</h4>
                    <ul>
                        <li>Earn 1 point per $1 spent</li>
                        <li>100 points = Free movie ticket</li>
                        <li>50 points = Free snack combo</li>
                        <li>Double points on your birthday month</li>
                    </ul>
                </div>

                <a href="{{ route('register') }}" class="promotion-cta">Start Earning</a>
                <p class="validity">Points never expire</p>
            </div>
        </div>
    </div>

    <!-- Student Deals Tab -->
    <div id="student-deals" class="tab-content">
        <div class="promotion-grid">
            <div class="promotion-card">
                <h3><span class="icon">🎓</span> Student Discount</h3>
                <p>Show your student ID and enjoy 20% off every Tuesday and Wednesday!</p>
                
                <div class="promotion-details">
                    <h4>Eligibility:</h4>
                    <ul>
                        <li>Valid student ID required</li>
                        <li>High school and college students</li>
                        <li>Applies to all showtimes</li>
                        <li>Can be combined with matinee pricing</li>
                    </ul>
                </div>

                <a href="{{ route('now_showing') }}" class="promotion-cta">Get Student Card</a>
                <p class="validity">Available: Every Tuesday & Wednesday</p>
            </div>

            <div class="promotion-card">
                <h3><span class="icon">📚</span> Study Break Special</h3>
                <p>Take a break from studying! Special pricing for students during exam season.</p>
                
                <div class="promotion-details">
                    <h4>Special Offer:</h4>
                    <ul>
                        <li>$5 tickets for all movies before 5 PM</li>
                        <li>Free study room rental (2 hours)</li>
                        <li>Discounted coffee and snacks</li>
                        <li>Valid during midterm and finals weeks</li>
                    </ul>
                </div>

                <a href="{{ route('now_showing') }}" class="promotion-cta">Book Now</a>
                <p class="validity">During academic exam periods</p>
            </div>

            <div class="promotion-card">
                <h3><span class="icon">👥</span> Student Group Discount</h3>
                <p>Bring your friends! Groups of 5+ students get additional 15% off.</p>
                
                <div class="promotion-details">
                    <h4>Group Benefits:</h4>
                    <ul>
                        <li>Minimum 5 students required</li>
                        <li>15% off regular student price</li>
                        <li>Reserved group seating</li>
                        <li>Group snack packages available</li>
                    </ul>
                </div>

                <a href="{{ route('now_showing') }}" class="promotion-cta">Book Group</a>
                <p class="validity">Advance booking required</p>
            </div>
        </div>
    </div>

    <!-- Seasonal Offers Tab -->
    <div id="seasonal" class="tab-content">
        <div class="promotion-grid">
            <div class="promotion-card">
                <h3><span class="icon">🎃</span> Holiday Special</h3>
                <p>Celebrate holidays with special movie packages and themed events!</p>
                
                <div class="promotion-details">
                    <h4>Holiday Perks:</h4>
                    <ul>
                        <li>Buy 1 Get 1 Free on major holidays</li>
                        <li>Special themed movie marathons</li>
                        <li>Holiday-themed snack combos</li>
                        <li>Free photo booth access</li>
                    </ul>
                </div>

                <a href="{{ route('upcoming_movies') }}" class="promotion-cta">View Events</a>
                <p class="validity">Major holidays throughout the year</p>
            </div>

            <div class="promotion-card">
                <h3><span class="icon">❄️</span> Winter Season Pass</h3>
                <p>Stay warm with unlimited movies during winter season!</p>
                
                <div class="promotion-details">
                    <h4>Pass Includes:</h4>
                    <ul>
                        <li>Unlimited movie tickets (Dec - Feb)</li>
                        <li>20% off all concessions</li>
                        <li>Priority booking for blockbusters</li>
                        <li>Exclusive winter-themed events</li>
                    </ul>
                </div>

                <a href="{{ route('register') }}" class="promotion-cta">Buy Pass</a>
                <p class="validity">Season pass: $99.99</p>
            </div>

            <div class="promotion-card">
                <h3><span class="icon">💝</span> Valentine's Special</h3>
                <p>Perfect date package for couples this Valentine's Day!</p>
                
                <div class="promotion-details">
                    <h4>Romance Package:</h4>
                    <ul>
                        <li>2 Premium Couple Seats</li>
                        <li>Champagne & Chocolate Box</li>
                        <li>Complimentary roses</li>
                        <li>Private theater experience available</li>
                    </ul>
                </div>

                <a href="{{ route('now_showing') }}" class="promotion-cta">Book Romance</a>
                <p class="validity">February 10-16, 2026</p>
            </div>
        </div>
    </div>
</div>

<script>
/**
 * Tab switching functionality
 * 
 * @param {Event} event - Click event
 * @param {string} tabName - ID of tab to show
 */
function openTab(event, tabName) {
    // Hide all tab contents
    const tabContents = document.getElementsByClassName('tab-content');
    for (let i = 0; i < tabContents.length; i++) {
        tabContents[i].classList.remove('active');
    }

    // Remove active class from all buttons
    const tabBtns = document.getElementsByClassName('tab-btn');
    for (let i = 0; i < tabBtns.length; i++) {
        tabBtns[i].classList.remove('active');
    }

    // Show current tab and mark button as active
    document.getElementById(tabName).classList.add('active');
    event.currentTarget.classList.add('active');
}
</script>
@endsection
