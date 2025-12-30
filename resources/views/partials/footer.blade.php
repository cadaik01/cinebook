<footer class="footer">
    <div class="footer-content">
        <div class="container">
            <div class="row">
                <!-- Company Info -->
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="footer-section">
                        <div class="footer-brand">
                            <img src="{{ asset('images/tca-cine-logo.jpg') }}" alt="TCA Cine Logo" class="footer-logo">
                            <h3 class="footer-title">TCA Cine</h3>
                        </div>
                        <p class="footer-description">
                            Your ultimate cinema destination. Experience the magic of movies with premium comfort, 
                            state-of-the-art technology, and unforgettable entertainment.
                        </p>
                        <div class="social-links">
                            <a href="#" class="social-link" title="Facebook">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            <a href="#" class="social-link" title="Twitter">
                                <i class="fab fa-twitter"></i>
                            </a>
                            <a href="#" class="social-link" title="Instagram">
                                <i class="fab fa-instagram"></i>
                            </a>
                            <a href="#" class="social-link" title="YouTube">
                                <i class="fab fa-youtube"></i>
                            </a>
                            <a href="#" class="social-link" title="LinkedIn">
                                <i class="fab fa-linkedin-in"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Quick Links -->
                <div class="col-lg-2 col-md-6 mb-4">
                    <div class="footer-section">
                        <h4 class="footer-heading">Quick Links</h4>
                        <ul class="footer-links">
                            <li><a href="">Home</a></li>
                            <li><a href="">Now Showing</a></li>
                            <li><a href="">Upcoming</a></li>
                            <li><a href="">Reviews</a></li>
                            <li><a href="">About Us</a></li>
                            <li><a href="">Contact</a></li>
                        </ul>
                    </div>
                </div>

                <!-- Movie Categories -->
                <div class="col-lg-2 col-md-6 mb-4">
                    <div class="footer-section">
                        <h4 class="footer-heading">Categories</h4>
                        <ul class="footer-links">
                            <li><a href="#">Action</a></li>
                            <li><a href="#">Comedy</a></li>
                            <li><a href="#">Drama</a></li>
                            <li><a href="#">Horror</a></li>
                            <li><a href="#">Romance</a></li>
                            <li><a href="#">Sci-Fi</a></li>
                        </ul>
                    </div>
                </div>

                <!-- Contact Info -->
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="footer-section">
                        <h4 class="footer-heading">Contact Info</h4>
                        <div class="contact-info">
                            <div class="contact-item">
                                <i class="fas fa-map-marker-alt"></i>
                                <div>
                                    <p><strong>Address:</strong></p>
                                    <p>21A Bis Phu Dong Thien Vuong Street<br>Ho Chi Minh City, Vietnam</p>
                                </div>
                            </div>
                            <div class="contact-item">
                                <i class="fas fa-phone"></i>
                                <div>
                                    <p><strong>Phone:</strong></p>
                                    <p>+84 123 456 789</p>
                                </div>
                            </div>
                            <div class="contact-item">
                                <i class="fas fa-envelope"></i>
                                <div>
                                    <p><strong>Email:</strong></p>
                                    <p>info@tcacine.com</p>
                                </div>
                            </div>
                            <div class="contact-item">
                                <i class="fas fa-clock"></i>
                                <div>
                                    <p><strong>Hours:</strong></p>
                                    <p>Daily: 9:00 AM - 11:00 PM</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Newsletter Subscription -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="newsletter-section">
                        <div class="newsletter-content">
                            <h4 class="newsletter-title">
                                <i class="fas fa-envelope-open"></i>
                                Stay Updated with Latest Movies
                            </h4>
                            <p class="newsletter-text">Subscribe to our newsletter and never miss the latest movie releases and exclusive offers!</p>
                        </div>
                        <form class="newsletter-form" action="" method="POST">
                            @csrf
                            <div class="input-group">
                                <input type="email" name="email" class="form-control newsletter-input" 
                                       placeholder="Enter your email address" required>
                                <button type="submit" class="btn newsletter-btn">
                                    <i class="fas fa-paper-plane"></i>
                                    Subscribe
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer Bottom -->
    <div class="footer-bottom">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="copyright">
                        &copy; {{ date('Y') }} TCA Cine. All rights reserved.
                    </p>
                </div>
                <div class="col-md-6">
                    <ul class="footer-bottom-links">
                        <li><a href="">Privacy Policy</a></li>
                        <li><a href="">Terms of Service</a></li>
                        <li><a href="">Sitemap</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</footer>

<style>
/* Footer Styles */
.footer {
    background: linear-gradient(135deg, #0f0f23, #1a1a2e);
    color: #ecf0f1;
    margin-top: auto;
}

.footer-content {
    padding: 3rem 0 2rem;
}

.footer-section {
    height: 100%;
}

/* Footer Brand */
.footer-brand {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 1rem;
}

.footer-logo {
    height: 35px;
    width: auto;
}

.footer-title {
    color: #f39c12;
    font-size: 1.5rem;
    font-weight: bold;
    margin: 0;
    font-family: 'Arial Black', Arial, sans-serif;
}

.footer-description {
    color: #bdc3c7;
    line-height: 1.6;
    margin-bottom: 1.5rem;
}

/* Social Links */
.social-links {
    display: flex;
    gap: 1rem;
}

.social-link {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    background-color: rgba(243, 156, 18, 0.1);
    color: #f39c12;
    border-radius: 50%;
    text-decoration: none;
    transition: all 0.3s ease;
    font-size: 1.1rem;
}

.social-link:hover {
    background-color: #f39c12;
    color: white;
    transform: translateY(-3px);
    box-shadow: 0 4px 12px rgba(243, 156, 18, 0.3);
}

/* Footer Headings */
.footer-heading {
    color: #f39c12;
    font-size: 1.2rem;
    font-weight: 600;
    margin-bottom: 1.5rem;
    position: relative;
}

.footer-heading::after {
    content: '';
    position: absolute;
    bottom: -8px;
    left: 0;
    width: 30px;
    height: 2px;
    background-color: #f39c12;
}

/* Footer Links */
.footer-links {
    list-style: none;
    padding: 0;
}

.footer-links li {
    margin-bottom: 0.75rem;
}

.footer-links a {
    color: #bdc3c7;
    text-decoration: none;
    transition: all 0.3s ease;
    display: inline-block;
}

.footer-links a:hover {
    color: #f39c12;
    transform: translateX(5px);
}

/* Contact Info */
.contact-info .contact-item {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.contact-item i {
    color: #f39c12;
    font-size: 1.1rem;
    margin-top: 0.2rem;
    min-width: 20px;
}

.contact-item p {
    margin: 0;
    color: #bdc3c7;
    line-height: 1.4;
}

.contact-item strong {
    color: #ecf0f1;
}

/* Newsletter Section */
.newsletter-section {
    background: linear-gradient(135deg, rgba(243, 156, 18, 0.1), rgba(230, 126, 34, 0.1));
    border-radius: 12px;
    padding: 2rem;
    text-align: center;
    border: 1px solid rgba(243, 156, 18, 0.2);
}

.newsletter-title {
    color: #f39c12;
    font-size: 1.4rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.newsletter-title i {
    margin-right: 0.5rem;
}

.newsletter-text {
    color: #bdc3c7;
    margin-bottom: 1.5rem;
}

.newsletter-form {
    max-width: 400px;
    margin: 0 auto;
}

.newsletter-input {
    background-color: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(243, 156, 18, 0.3);
    color: #ecf0f1;
    padding: 0.75rem 1rem;
}

.newsletter-input:focus {
    background-color: rgba(255, 255, 255, 0.15);
    border-color: #f39c12;
    color: #ecf0f1;
    box-shadow: 0 0 0 0.25rem rgba(243, 156, 18, 0.25);
}

.newsletter-input::placeholder {
    color: #bdc3c7;
}

.newsletter-btn {
    background-color: #f39c12;
    border: none;
    color: white;
    padding: 0.75rem 1.5rem;
    font-weight: 500;
    transition: all 0.3s ease;
}

.newsletter-btn:hover {
    background-color: #e67e22;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(243, 156, 18, 0.3);
}

/* Footer Bottom */
.footer-bottom {
    background-color: rgba(0, 0, 0, 0.3);
    padding: 1.5rem 0;
    border-top: 1px solid rgba(243, 156, 18, 0.2);
}

.copyright {
    margin: 0;
    color: #bdc3c7;
}

.footer-bottom-links {
    list-style: none;
    padding: 0;
    margin: 0;
    display: flex;
    justify-content: flex-end;
    gap: 2rem;
}

.footer-bottom-links a {
    color: #bdc3c7;
    text-decoration: none;
    transition: color 0.3s ease;
}

.footer-bottom-links a:hover {
    color: #f39c12;
}

/* Responsive Design */
@media (max-width: 768px) {
    .footer-content {
        padding: 2rem 0 1.5rem;
    }
    
    .newsletter-section {
        padding: 1.5rem;
    }
    
    .newsletter-form .input-group {
        flex-direction: column;
        gap: 0.75rem;
    }
    
    .newsletter-btn {
        width: 100%;
    }
    
    .footer-bottom-links {
        justify-content: center;
        margin-top: 1rem;
        flex-wrap: wrap;
        gap: 1rem;
    }
    
    .social-links {
        justify-content: center;
    }
    
    .contact-item {
        text-align: left;
    }
}

@media (max-width: 576px) {
    .footer-brand {
        justify-content: center;
        text-align: center;
    }
    
    .footer-section {
        text-align: center;
        margin-bottom: 2rem;
    }
    
    .footer-links,
    .contact-info {
        text-align: center;
    }
    
    .contact-item {
        justify-content: center;
        text-align: center;
    }
}
</style>
