@extends('layouts.app')

@section('css')
<style>
    :root {
        --primary-color: #2563eb;
        --secondary-color: #64748b;
        --accent-color: #0ea5e9;
        --background-light: #f8fafc;
        --text-dark: #1e293b;
        --text-muted: #64748b;
    }

    body {
        font-family: 'Inter', system-ui, -apple-system, sans-serif;
        line-height: 1.6;
        color: var(--text-dark);
    }

    .hero-section {
        background: linear-gradient(135deg, var(--background-light) 0%, #ffffff 100%);
        min-height: 90vh;
        display: flex;
        align-items: center;
    }

    .btn-primary-custom {
        background-color: var(--primary-color);
        border: none;
        padding: 14px 28px;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-block;
    }

    .btn-primary-custom:hover {
        background-color: #1d4ed8;
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(37, 99, 235, 0.3);
    }

    .btn-outline-custom {
        border: 2px solid var(--primary-color);
        color: var(--primary-color);
        padding: 12px 28px;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-block;
        background: transparent;
    }

    .btn-outline-custom:hover {
        background-color: var(--primary-color);
        color: white;
        transform: translateY(-2px);
    }

    .feature-card {
        background: white;
        border-radius: 16px;
        padding: 2rem;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
        border: 1px solid #e2e8f0;
        height: 100%;
    }

    .feature-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
    }

    .feature-icon {
        width: 64px;
        height: 64px;
        background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1.5rem;
    }

    .pricing-card {
        background: white;
        border-radius: 16px;
        padding: 2.5rem 2rem;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
        border: 2px solid #e2e8f0;
        height: 100%;
        position: relative;
    }

    .pricing-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
    }

    .pricing-card.featured {
        border-color: var(--primary-color);
        transform: scale(1.05);
    }

    .pricing-card.featured::before {
        content: "Most Popular";
        position: absolute;
        top: -12px;
        left: 50%;
        transform: translateX(-50%);
        background: var(--primary-color);
        color: white;
        padding: 6px 20px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
    }

    .section-padding {
        padding: 5rem 0;
    }

    .text-gradient {
        background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .stats-section {
        background: var(--primary-color);
        color: white;
    }

    .stat-item {
        text-align: center;
        padding: 2rem 1rem;
    }

    .contact-section {
        background: var(--background-light);
    }

    /* Team Section Styles */
    .team-section {
        background: #f8f9fa;
    }
    
    .team-card {
        background: white;
        border-radius: 20px;
        padding: 2rem;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        transition: all 0.4s ease;
        border: 1px solid #e2e8f0;
        height: 100%;
        text-align: center;
        position: relative;
        overflow: hidden;
    }
    
    .team-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
    }
    
    .team-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
    }
    
    .team-photo {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid #f1f5f9;
        margin: 0 auto 1.5rem;
        transition: all 0.3s ease;
    }
    
    .team-card:hover .team-photo {
        border-color: var(--primary-color);
        transform: scale(1.05);
    }
    
    .team-name {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--text-dark);
        margin-bottom: 0.5rem;
    }
    
    .team-role {
        display: inline-flex;
        align-items: center;
        background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
        color: white;
        padding: 0.4rem 1rem;
        border-radius: 25px;
        font-size: 0.85rem;
        font-weight: 600;
        margin-bottom: 1rem;
    }
    
    .team-major {
        color: var(--text-muted);
        font-size: 1rem;
        margin-bottom: 1rem;
        font-weight: 500;
    }
    
    .team-bio {
        color: var(--text-muted);
        font-size: 0.95rem;
        line-height: 1.6;
        margin-bottom: 1.5rem;
    }
    
    .team-contact {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }
    
    .contact-item {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        color: var(--text-muted);
        text-decoration: none;
        transition: all 0.3s ease;
        font-size: 0.9rem;
    }
    
    .contact-item:hover {
        color: var(--primary-color);
        transform: translateX(5px);
    }
    
    .contact-icon {
        width: 20px;
        height: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: var(--background-light);
        border-radius: 50%;
        transition: all 0.3s ease;
    }
    
    .contact-item:hover .contact-icon {
        background: var(--primary-color);
        color: white;
    }

    @media (max-width: 768px) {
        .hero-section {
            min-height: 70vh;
            text-align: center;
        }
        
        .pricing-card.featured {
            transform: none;
        }
    }
</style>
@endsection

@section('content')
<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="display-4 fw-bold mb-4">
                    Manage Your Properties 
                    <span class="text-gradient">Like a Pro</span>
                </h1>
                <p class="lead text-muted mb-4 fs-5">
                    jhome is the complete property management solution for real estate companies, 
                    property owners, and rental managers. Automate contracts, track payments, 
                    and manage tenants all in one powerful platform.
                </p>
                <div class="d-flex gap-3 flex-wrap">
                    <a href="#pricing" class="btn btn-primary-custom btn-lg">
                        <i class="ri-rocket-line me-2"></i>Get Started
                    </a>
                    <a href="#contact" class="btn btn-outline-custom btn-lg">
                        <i class="ri-calendar-line me-2"></i>Book Demo
                    </a>
                </div>
            </div>
            <div class="col-lg-6 text-center">
                <div class="position-relative">
                    <img src="https://images.unsplash.com/photo-1560518883-ce09059eeffa?w=600&q=80" 
                         alt="Property Management Dashboard" 
                         class="img-fluid rounded-4 shadow-lg"
                         style="max-height: 500px; object-fit: cover;">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="stats-section py-5">
    <div class="container">
        <div class="row">
            <div class="col-md-3 col-6">
                <div class="stat-item">
                    <h3 class="display-6 fw-bold">500+</h3>
                    <p class="mb-0">Properties Managed</p>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-item">
                    <h3 class="display-6 fw-bold">50+</h3>
                    <p class="mb-0">Happy Clients</p>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-item">
                    <h3 class="display-6 fw-bold">99.9%</h3>
                    <p class="mb-0">Uptime</p>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-item">
                    <h3 class="display-6 fw-bold">24/7</h3>
                    <p class="mb-0">Support</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- About Us Section -->
<section id="about" class="section-padding">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 mb-5 mb-lg-0">
                <h2 class="h1 fw-bold mb-4">
                    Built for Modern 
                    <span class="text-gradient">Property Management</span>
                </h2>
                <p class="text-muted mb-4 fs-5">
                    jhome simplifies property management with a smart three-tier system designed 
                    for efficiency and control. Our platform serves property owners, account managers, 
                    and tenants with tailored access levels.
                </p>
                <div class="row g-4">
                    <div class="col-12">
                        <div class="d-flex align-items-start">
                            <div class="feature-icon me-3" style="width: 48px; height: 48px;">
                                <i class="ri-user-star-line text-white fs-5"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-2">Property Owners</h5>
                                <p class="text-muted mb-0">Super admins with full control over properties, users, and system settings.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="d-flex align-items-start">
                            <div class="feature-icon me-3" style="width: 48px; height: 48px;">
                                <i class="ri-user-settings-line text-white fs-5"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-2">Account Managers</h5>
                                <p class="text-muted mb-0">Sub-users who assist owners in managing day-to-day operations and tenant relations.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="d-flex align-items-start">
                            <div class="feature-icon me-3" style="width: 48px; height: 48px;">
                                <i class="ri-user-line text-white fs-5"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-2">Tenants</h5>
                                <p class="text-muted mb-0">View-only access to contracts, payments, and property details. Invited by owners only.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <img src="https://images.unsplash.com/photo-1554224155-6726b3ff858f?w=600&q=80" 
                     alt="Team collaboration" 
                     class="img-fluid rounded-4 shadow-lg">
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section id="features" class="section-padding" style="background: var(--background-light);">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="h1 fw-bold mb-3">
                Powerful Features for 
                <span class="text-gradient">Complete Control</span>
            </h2>
            <p class="text-muted fs-5 mx-auto" style="max-width: 600px;">
                Everything you need to manage properties efficiently, automate workflows, 
                and keep tenants happy.
            </p>
        </div>
        
        <div class="row g-4">
            <div class="col-lg-3 col-md-6">
                <div class="feature-card text-center">
                    <div class="feature-icon mx-auto">
                        <i class="ri-file-text-line text-white fs-3"></i>
                    </div>
                    <h5 class="fw-bold mb-3">Contract Automation</h5>
                    <p class="text-muted">Generate, manage, and renew contracts automatically with customizable templates and digital signatures.</p>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <div class="feature-card text-center">
                    <div class="feature-icon mx-auto">
                        <i class="ri-building-line text-white fs-3"></i>
                    </div>
                    <h5 class="fw-bold mb-3">Property Overview</h5>
                    <p class="text-muted">Comprehensive dashboard to monitor all properties, units, occupancy rates, and maintenance schedules.</p>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <div class="feature-card text-center">
                    <div class="feature-icon mx-auto">
                        <i class="ri-money-dollar-circle-line text-white fs-3"></i>
                    </div>
                    <h5 class="fw-bold mb-3">Payment Reports</h5>
                    <p class="text-muted">Track payments, generate invoices, and monitor financial performance with detailed analytics.</p>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <div class="feature-card text-center">
                    <div class="feature-icon mx-auto">
                        <i class="ri-team-line text-white fs-3"></i>
                    </div>
                    <h5 class="fw-bold mb-3">Multi-User Access</h5>
                    <p class="text-muted">Role-based permissions ensure secure access for owners, managers, and tenants with appropriate controls.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Pricing Section -->
<section id="pricing" class="section-padding">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="h1 fw-bold mb-3">
                Simple, Transparent 
                <span class="text-gradient">Pricing</span>
            </h2>
            <p class="text-muted fs-5 mx-auto" style="max-width: 600px;">
                Choose the plan that fits your business size. All plans include our core features 
                with premium support.
            </p>
        </div>
        
        <div class="row g-4 justify-content-center">
            <div class="col-lg-4 col-md-6">
                <div class="pricing-card">
                    <div class="text-center mb-4">
                        <h4 class="fw-bold mb-2">Starter</h4>
                        <p class="text-muted">Perfect for small property owners</p>
                        <div class="pricing-amount">
                            <span class="h2 fw-bold">15</span>
                            <span class="text-muted fs-5">JOD/month</span>
                        </div>
                    </div>
                    <ul class="list-unstyled mb-4">
                        <li class="mb-2"><i class="ri-check-line text-success me-2"></i>Up to 10 properties</li>
                        <li class="mb-2"><i class="ri-check-line text-success me-2"></i>Contract management</li>
                        <li class="mb-2"><i class="ri-check-line text-success me-2"></i>Basic reporting</li>
                        <li class="mb-2"><i class="ri-check-line text-success me-2"></i>Email support</li>
                        <li class="mb-2"><i class="ri-check-line text-success me-2"></i>2 manager accounts</li>
                    </ul>
                    <a href="#contact" class="btn btn-outline-custom w-100">Subscribe Now</a>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6">
                <div class="pricing-card featured">
                    <div class="text-center mb-4">
                        <h4 class="fw-bold mb-2">Professional</h4>
                        <p class="text-muted">Ideal for growing businesses</p>
                        <div class="pricing-amount">
                            <span class="h2 fw-bold">39</span>
                            <span class="text-muted fs-5">JOD/month</span>
                        </div>
                    </div>
                    <ul class="list-unstyled mb-4">
                        <li class="mb-2"><i class="ri-check-line text-success me-2"></i>Up to 50 properties</li>
                        <li class="mb-2"><i class="ri-check-line text-success me-2"></i>Advanced contract automation</li>
                        <li class="mb-2"><i class="ri-check-line text-success me-2"></i>Detailed analytics</li>
                        <li class="mb-2"><i class="ri-check-line text-success me-2"></i>Priority support</li>
                        <li class="mb-2"><i class="ri-check-line text-success me-2"></i>5 manager accounts</li>
                        <li class="mb-2"><i class="ri-check-line text-success me-2"></i>Custom reports</li>
                    </ul>
                    <a href="#contact" class="btn btn-primary-custom w-100">Subscribe Now</a>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6">
                <div class="pricing-card">
                    <div class="text-center mb-4">
                        <h4 class="fw-bold mb-2">Enterprise</h4>
                        <p class="text-muted">For large-scale operations</p>
                        <div class="pricing-amount">
                            <span class="h2 fw-bold">Custom</span>
                            <span class="text-muted fs-5">Quote</span>
                        </div>
                    </div>
                    <ul class="list-unstyled mb-4">
                        <li class="mb-2"><i class="ri-check-line text-success me-2"></i>Unlimited properties</li>
                        <li class="mb-2"><i class="ri-check-line text-success me-2"></i>Full automation suite</li>
                        <li class="mb-2"><i class="ri-check-line text-success me-2"></i>Enterprise analytics</li>
                        <li class="mb-2"><i class="ri-check-line text-success me-2"></i>24/7 phone support</li>
                        <li class="mb-2"><i class="ri-check-line text-success me-2"></i>Unlimited manager accounts</li>
                        <li class="mb-2"><i class="ri-check-line text-success me-2"></i>White-label options</li>
                    </ul>
                    <a href="#contact" class="btn btn-outline-custom w-100">Contact Sales</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Meet Our Team Section -->
<section id="team" class="team-section section-padding">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="h1 fw-bold mb-3">
                Meet Our 
                <span class="text-gradient">Team</span>
            </h2>
            <p class="text-muted fs-5 mx-auto" style="max-width: 700px;">
                The brilliant minds behind JHome - A passionate team of university students from WISE University 
                dedicated to revolutionizing property management through innovative technology solutions.
            </p>
        </div>
        
        <div class="row g-4 justify-content-center">
            <!-- Team Member 1 -->
            <div class="col-lg-4 col-md-6">
                <div class="team-card">
                    <img src="https://via.placeholder.com/150/2563eb/ffffff?text=A.H" 
                         alt="Abedal-Aziz Mohammad Rabah Hashlamoun" 
                         class="team-photo">
                    
                    <h3 class="team-name">Abedal-Aziz Mohammad Rabah Hashlamoun</h3>
                    
                    <div class="team-role">
                        <i class="ri-code-s-slash-line me-2"></i>
                        Lead Developer & Co-Founder
                    </div>
                    
                    <p class="team-major">Computer Science Student</p>
                    
                    <p class="team-bio">
                        Passionate full-stack developer with expertise in Laravel and modern web technologies. 
                        Leading the technical vision and architectural design of JHome's innovative platform.
                    </p>
                    
                    <div class="team-contact">
                        <a href="mailto:3210601052@std.wise.edu.jo" class="contact-item">
                            <div class="contact-icon">
                                <i class="ri-mail-line"></i>
                            </div>
                            3210601052@std.wise.edu.jo
                        </a>
                        <a href="tel:+962798539958" class="contact-item">
                            <div class="contact-icon">
                                <i class="ri-phone-line"></i>
                            </div>
                            +962 798 539 958
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Team Member 2 -->
            <div class="col-lg-4 col-md-6">
                <div class="team-card">
                    <img src="https://via.placeholder.com/150/0ea5e9/ffffff?text=O.A" 
                         alt="Osaid Salah Abu Al Haj" 
                         class="team-photo">
                    
                    <h3 class="team-name">Osaid Salah Abu Al Haj</h3>
                    
                    <div class="team-role">
                        <i class="ri-tools-line me-2"></i>
                        Software Engineer & Co-Founder
                    </div>
                    
                    <p class="team-major">Software Engineering Student</p>
                    
                    <p class="team-bio">
                        Expert in software architecture and development methodologies with a focus on scalable solutions. 
                        Driving technical excellence and implementing robust development practices.
                    </p>
                    
                    <div class="team-contact">
                        <a href="mailto:3210605089@std.wise.edu.jo" class="contact-item">
                            <div class="contact-icon">
                                <i class="ri-mail-line"></i>
                            </div>
                            3210605089@std.wise.edu.jo
                        </a>
                        <a href="tel:+962788389585" class="contact-item">
                            <div class="contact-icon">
                                <i class="ri-phone-line"></i>
                            </div>
                            +962 788 389 585
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Team Member 3 -->
            <div class="col-lg-4 col-md-6">
                <div class="team-card">
                    <img src="https://via.placeholder.com/150/64748b/ffffff?text=K.B" 
                         alt="Khaleel Ibrahim Saeed Birjas" 
                         class="team-photo">
                    
                    <h3 class="team-name">Khaleel Ibrahim Saeed Birjas</h3>
                    
                    <div class="team-role">
                        <i class="ri-shield-check-line me-2"></i>
                        Network Security & Co-Founder
                    </div>
                    
                    <p class="team-major">Computer Network Systems Student</p>
                    
                    <p class="team-bio">
                        Specialist in network infrastructure, cybersecurity, and system administration. 
                        Ensuring robust security protocols and reliable network architecture for our platform.
                    </p>
                    
                    <div class="team-contact">
                        <a href="mailto:3210603085@std.wise.edu.jo" class="contact-item">
                            <div class="contact-icon">
                                <i class="ri-mail-line"></i>
                            </div>
                            3210603085@std.wise.edu.jo
                        </a>
                        <a href="tel:+962781850639" class="contact-item">
                            <div class="contact-icon">
                                <i class="ri-phone-line"></i>
                            </div>
                            +962 781 850 639
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Contact Section -->
<section id="contact" class="contact-section section-padding">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 mb-5 mb-lg-0">
                <h2 class="h1 fw-bold mb-4">
                    Ready to Get Started?
                </h2>
                <p class="text-muted mb-4 fs-5">
                    Join hundreds of property owners who trust jhome to manage their portfolios. 
                    Contact us today for a personalized demo.
                </p>
                
                <div class="row g-4 mb-4">
                    <div class="col-12">
                        <div class="d-flex align-items-center">
                            <div class="feature-icon me-3" style="width: 48px; height: 48px;">
                                <i class="ri-phone-line text-white fs-5"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-1">Phone Support</h6>
                                <p class="text-muted mb-0">+962 6 123 4567</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="d-flex align-items-center">
                            <div class="feature-icon me-3" style="width: 48px; height: 48px;">
                                <i class="ri-mail-line text-white fs-5"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-1">Email Support</h6>
                                <p class="text-muted mb-0">support@jhome.jo</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="d-flex align-items-center">
                            <div class="feature-icon me-3" style="width: 48px; height: 48px;">
                                <i class="ri-map-pin-line text-white fs-5"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-1">Office Location</h6>
                                <p class="text-muted mb-0">Amman, Jordan</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="d-flex gap-3 flex-wrap">
                    <a href="/login" class="btn btn-primary-custom">
                        <i class="ri-login-box-line me-2"></i>Login to Dashboard
                    </a>
                    <a href="#pricing" class="btn btn-outline-custom">
                        <i class="ri-arrow-up-line me-2"></i>View Pricing
                    </a>
                </div>
            </div>
            
            <div class="col-lg-6">
                <div class="bg-white p-4 rounded-4 shadow-lg">
                    <h4 class="fw-bold mb-4">Request a Demo</h4>
                    <form>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="firstName" class="form-label">First Name</label>
                                <input type="text" class="form-control form-control-custom" id="firstName" required>
                            </div>
                            <div class="col-md-6">
                                <label for="lastName" class="form-label">Last Name</label>
                                <input type="text" class="form-control form-control-custom" id="lastName" required>
                            </div>
                            <div class="col-12">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control form-control-custom" id="email" required>
                            </div>
                            <div class="col-12">
                                <label for="company" class="form-label">Company Name</label>
                                <input type="text" class="form-control form-control-custom" id="company">
                            </div>
                            <div class="col-12">
                                <label for="properties" class="form-label">Number of Properties</label>
                                <select class="form-control form-control-custom" id="properties">
                                    <option value="">Select range</option>
                                    <option value="1-10">1-10 properties</option>
                                    <option value="11-50">11-50 properties</option>
                                    <option value="51-100">51-100 properties</option>
                                    <option value="100+">100+ properties</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label for="message" class="form-label">Message</label>
                                <textarea class="form-control form-control-custom" id="message" rows="4" placeholder="Tell us about your property management needs..."></textarea>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary-custom w-100">
                                    <i class="ri-send-plane-line me-2"></i>Send Request
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Footer -->
<footer class="bg-dark text-white py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-4 mb-4 mb-lg-0">
                <h5 class="fw-bold mb-3">jhome</h5>
                <p class="text-muted">
                    The complete property management solution for modern real estate professionals.
                </p>
                <div class="d-flex gap-3">
                    <a href="#" class="text-muted fs-5"><i class="ri-facebook-fill"></i></a>
                    <a href="#" class="text-muted fs-5"><i class="ri-twitter-fill"></i></a>
                    <a href="#" class="text-muted fs-5"><i class="ri-linkedin-fill"></i></a>
                    <a href="#" class="text-muted fs-5"><i class="ri-instagram-fill"></i></a>
                </div>
            </div>
            <div class="col-lg-2 col-md-3 col-6 mb-4 mb-lg-0">
                <h6 class="fw-bold mb-3">Product</h6>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="#features" class="text-muted text-decoration-none">Features</a></li>
                    <li class="mb-2"><a href="#pricing" class="text-muted text-decoration-none">Pricing</a></li>
                    <li class="mb-2"><a href="#about" class="text-muted text-decoration-none">About</a></li>
                    <li class="mb-2"><a href="/login" class="text-muted text-decoration-none">Login</a></li>
                </ul>
            </div>
            <div class="col-lg-2 col-md-3 col-6 mb-4 mb-lg-0">
                <h6 class="fw-bold mb-3">Support</h6>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="#contact" class="text-muted text-decoration-none">Contact</a></li>
                    <li class="mb-2"><a href="#" class="text-muted text-decoration-none">Help Center</a></li>
                    <li class="mb-2"><a href="#" class="text-muted text-decoration-none">Documentation</a></li>
                    <li class="mb-2"><a href="#" class="text-muted text-decoration-none">API</a></li>
                </ul>
            </div>
            <div class="col-lg-2 col-md-3 col-6 mb-4 mb-lg-0">
                <h6 class="fw-bold mb-3">Legal</h6>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="#" class="text-muted text-decoration-none">Privacy Policy</a></li>
                    <li class="mb-2"><a href="#" class="text-muted text-decoration-none">Terms of Service</a></li>
                    <li class="mb-2"><a href="#" class="text-muted text-decoration-none">Cookie Policy</a></li>
                </ul>
            </div>
            <div class="col-lg-2 col-md-3 col-6">
                <h6 class="fw-bold mb-3">Company</h6>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="#about" class="text-muted text-decoration-none">About Us</a></li>
                    <li class="mb-2"><a href="#" class="text-muted text-decoration-none">Careers</a></li>
                    <li class="mb-2"><a href="#" class="text-muted text-decoration-none">Blog</a></li>
                    <li class="mb-2"><a href="#contact" class="text-muted text-decoration-none">Contact</a></li>
                </ul>
            </div>
        </div>
        <hr class="my-4 border-secondary">
        <div class="row align-items-center">
            <div class="col-md-6">
                <p class="text-muted mb-0">&copy; 2025 jhome. All rights reserved.</p>
            </div>
            <div class="col-md-6 text-md-end">
                <p class="text-muted mb-0">Made with ❤️ in Jordan</p>
            </div>
        </div>
    </div>
</footer>
@endsection

@section('js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // Contact form submission
    const contactForm = document.querySelector('#contact form');
    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Get form data
            const formData = new FormData(this);
            const data = Object.fromEntries(formData);
            
            // Simple validation
            if (!data.firstName || !data.lastName || !data.email) {
                alert('Please fill in all required fields.');
                return;
            }
            
            // Show success message (in real implementation, send to server)
            alert('Thank you for your interest! We\'ll contact you within 24 hours to schedule your demo.');
            this.reset();
        });
    }

    // Add scroll effect for navbar if exists
    window.addEventListener('scroll', function() {
        const navbar = document.querySelector('.navbar');
        if (navbar) {
            if (window.scrollY > 100) {
                navbar.classList.add('navbar-scrolled');
            } else {
                navbar.classList.remove('navbar-scrolled');
            }
        }
    });
});
</script>
@endsection