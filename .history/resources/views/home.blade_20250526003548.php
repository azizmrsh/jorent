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

    .form-control-custom {
        border: 2px solid #e2e8f0;
        border-radius: 8px;
        padding: 12px 16px;
        font-size: 0.95rem;
        transition: all 0.3s ease;
        background: #fafbfc;
    }
    
    .form-control-custom:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        background: white;
        outline: none;
    }
    
    .form-label {
        font-weight: 600;
        color: var(--text-dark);
        margin-bottom: 0.5rem;
    }

    /* Enhanced Footer Styles */
    .footer-enhanced {
        position: relative;
        background: linear-gradient(135deg, #1e293b 0%, #334155 50%, #475569 100%);
        color: white;
        overflow: hidden;
    }
    
    .footer-gradient-overlay {
        background: linear-gradient(135deg, rgba(37, 99, 235, 0.1) 0%, rgba(14, 165, 233, 0.05) 100%);
        padding: 4rem 0 0;
        position: relative;
        z-index: 2;
    }
    
    .footer-main {
        margin-bottom: 2rem;
    }
    
    /* Brand Section */
    .footer-brand {
        position: relative;
    }
    
    .brand-logo {
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    
    .logo-icon {
        width: 50px;
        height: 50px;
        background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: white;
        box-shadow: 0 8px 20px rgba(37, 99, 235, 0.3);
    }
    
    .brand-name {
        font-size: 2rem;
        font-weight: 800;
        margin: 0;
        background: linear-gradient(135deg, #ffffff, #e2e8f0);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    
    .brand-description {
        color: #cbd5e1;
        font-size: 1rem;
        line-height: 1.6;
        margin: 1rem 0;
    }
    
    /* Stats Pills */
    .footer-stats {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
    }
    
    .stat-pill {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        background: rgba(255, 255, 255, 0.1);
        padding: 0.5rem 1rem;
        border-radius: 25px;
        font-size: 0.85rem;
        font-weight: 600;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        transition: all 0.3s ease;
    }
    
    .stat-pill:hover {
        background: rgba(255, 255, 255, 0.15);
        transform: translateY(-2px);
    }
    
    .stat-pill i {
        color: var(--accent-color);
    }
    
    /* Social Links */
    .social-links {
        display: flex;
        gap: 1rem;
        margin-top: 1.5rem;
    }
    
    .social-link {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        text-decoration: none;
        transition: all 0.4s ease;
        border: 2px solid rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        position: relative;
        overflow: hidden;
    }
    
    .social-link::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, transparent, rgba(255, 255, 255, 0.1));
        transform: translateX(-100%);
        transition: transform 0.4s ease;
    }
    
    .social-link:hover::before {
        transform: translateX(0);
    }
    
    .social-link.facebook { color: #1877f2; }
    .social-link.twitter { color: #1da1f2; }
    .social-link.linkedin { color: #0077b5; }
    .social-link.instagram { color: #e4405f; }
    .social-link.github { color: #333; }
    
    .social-link:hover {
        transform: translateY(-3px) scale(1.1);
        border-color: currentColor;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
    }
    
    /* Footer Sections */
    .footer-section {
        position: relative;
    }
    
    .footer-title {
        font-size: 1.1rem;
        font-weight: 700;
        margin-bottom: 1.5rem;
        color: white;
        display: flex;
        align-items: center;
        position: relative;
    }
    
    .footer-title::after {
        content: '';
        position: absolute;
        bottom: -8px;
        left: 0;
        width: 30px;
        height: 3px;
        background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
        border-radius: 2px;
    }
    
    .footer-title i {
        color: var(--accent-color);
        font-size: 1rem;
    }
    
    /* Footer Links */
    .footer-links {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    
    .footer-links li {
        margin-bottom: 0.75rem;
    }
    
    .footer-links a {
        color: #cbd5e1;
        text-decoration: none;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        position: relative;
        padding-left: 1rem;
    }
    
    .footer-links a::before {
        content: '→';
        position: absolute;
        left: 0;
        opacity: 0;
        transform: translateX(-10px);
        transition: all 0.3s ease;
        color: var(--accent-color);
        font-weight: bold;
    }
    
    .footer-links a:hover {
        color: white;
        padding-left: 1.5rem;
    }
    
    .footer-links a:hover::before {
        opacity: 1;
        transform: translateX(0);
    }
    
    /* Special Link Styles */
    .badge-link .badge {
        background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
        color: white;
        font-size: 0.7rem;
        padding: 0.2rem 0.5rem;
        border-radius: 12px;
        margin-left: 0.5rem;
    }
    
    .external-link i {
        font-size: 0.8rem;
        margin-left: 0.3rem;
        opacity: 0.7;
    }
    
    .live-chat {
        position: relative;
    }
    
    .status-dot {
        position: absolute;
        top: -2px;
        right: -5px;
        width: 8px;
        height: 8px;
        background: #10b981;
        border-radius: 50%;
        animation: pulse 2s infinite;
    }
    
    .hiring-badge {
        background: linear-gradient(135deg, #10b981, #34d399);
        color: white;
        font-size: 0.7rem;
        padding: 0.2rem 0.5rem;
        border-radius: 12px;
        margin-left: 0.5rem;
        animation: glow 2s ease-in-out infinite alternate;
    }
    
    /* Contact Info */
    .contact-info {
        margin-bottom: 1.5rem;
    }
    
    .contact-item {
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
        margin-bottom: 1rem;
        padding: 0.75rem;
        background: rgba(255, 255, 255, 0.05);
        border-radius: 8px;
        transition: all 0.3s ease;
    }
    
    .contact-item:hover {
        background: rgba(255, 255, 255, 0.1);
        transform: translateX(5px);
    }
    
    .contact-item i {
        color: var(--accent-color);
        font-size: 1.1rem;
        margin-top: 0.2rem;
    }
    
    .contact-label {
        display: block;
        font-size: 0.8rem;
        color: #94a3b8;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .contact-item a {
        color: #e2e8f0;
        text-decoration: none;
        font-weight: 500;
    }
    
    .contact-item a:hover {
        color: white;
    }
    
    /* Legal Links */
    .legal-links {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .legal-link {
        color: #94a3b8;
        text-decoration: none;
        font-size: 0.85rem;
        transition: color 0.3s ease;
    }
    
    .legal-link:hover {
        color: var(--accent-color);
    }
    
    /* Newsletter Section */
    .newsletter-section {
        background: rgba(255, 255, 255, 0.05);
        padding: 2rem;
        border-radius: 16px;
        margin-bottom: 2rem;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }
    
    .newsletter-title {
        font-size: 1.25rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
        color: white;
        display: flex;
        align-items: center;
    }
    
    .newsletter-title i {
        color: var(--accent-color);
    }
    
    .newsletter-text {
        color: #cbd5e1;
        margin-bottom: 0;
        font-size: 0.95rem;
    }
    
    .newsletter-form .input-group {
        max-width: 400px;
        margin-left: auto;
    }
    
    .newsletter-input {
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        color: white;
        padding: 0.75rem 1rem;
        border-radius: 8px 0 0 8px;
    }
    
    .newsletter-input::placeholder {
        color: #94a3b8;
    }
    
    .newsletter-input:focus {
        background: rgba(255, 255, 255, 0.15);
        border-color: var(--accent-color);
        box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.2);
        color: white;
    }
    
    .newsletter-btn {
        background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
        border: none;
        color: white;
        padding: 0.75rem 1.5rem;
        border-radius: 0 8px 8px 0;
        font-weight: 600;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .newsletter-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(37, 99, 235, 0.3);
    }
    
    /* Footer Bottom */
    .footer-bottom {
        border-top: 1px solid rgba(255, 255, 255, 0.1);
        padding-top: 2rem;
        margin-top: 2rem;
    }
    
    .copyright {
        color: #cbd5e1;
        font-size: 0.95rem;
    }
    
    .brand-highlight {
        color: var(--accent-color);
        font-weight: 700;
    }
    
    .footer-meta {
        text-align: end;
    }
    
    .made-with {
        color: #cbd5e1;
        font-size: 0.95rem;
        margin-bottom: 0.5rem;
    }
    
    .heart {
        color: #ef4444;
        animation: heartbeat 1.5s ease-in-out infinite;
    }
    
    .jordan-flag {
        font-size: 1.1rem;
    }
    
    .tech-badges {
        display: flex;
        gap: 0.5rem;
        justify-content: flex-end;
        flex-wrap: wrap;
    }
    
    .tech-badge {
        background: rgba(255, 255, 255, 0.1);
        color: #e2e8f0;
        padding: 0.25rem 0.75rem;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
        border: 1px solid rgba(255, 255, 255, 0.1);
    }
    
    /* Floating Elements */
    .footer-decoration {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        pointer-events: none;
        z-index: 1;
    }
    
    .floating-elements {
        position: relative;
        width: 100%;
        height: 100%;
    }
    
    .floating-element {
        position: absolute;
        color: rgba(255, 255, 255, 0.1);
        font-size: 2rem;
        animation: float 8s ease-in-out infinite;
        animation-delay: var(--delay);
        left: var(--x);
        top: var(--y);
    }
    
    /* Animations */
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }
    
    @keyframes glow {
        from { box-shadow: 0 0 5px rgba(16, 185, 129, 0.5); }
        to { box-shadow: 0 0 20px rgba(16, 185, 129, 0.8); }
    }
    
    @keyframes heartbeat {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.1); }
    }
    
    @keyframes float {
        0%, 100% { transform: translate(0, 0) rotate(0deg); }
        33% { transform: translate(30px, -30px) rotate(120deg); }
        66% { transform: translate(-20px, 20px) rotate(240deg); }
    }

    /* Responsive Design for Footer */
    @media (max-width: 768px) {
        .footer-enhanced {
            text-align: center;
        }
        
        .footer-gradient-overlay {
            padding: 3rem 0 0;
        }
        
        .brand-logo {
            justify-content: center;
            margin-bottom: 1rem;
        }
        
        .footer-stats {
            justify-content: center;
        }
        
        .social-links {
            justify-content: center;
        }
        
        .footer-title {
            justify-content: center;
            text-align: center;
        }
        
        .footer-title::after {
            left: 50%;
            transform: translateX(-50%);
        }
        
        .footer-links {
            text-align: center;
        }
        
        .footer-links a {
            justify-content: center;
        }
        
        .contact-info {
            max-width: 300px;
            margin: 0 auto 1.5rem;
        }
        
        .contact-item {
            justify-content: center;
            text-align: left;
        }
        
        .newsletter-form .input-group {
            margin: 0;
        }
        
        .footer-meta {
            text-align: center;
        }
        
        .tech-badges {
            justify-content: center;
        }
        
        .floating-element {
            display: none;
        }
    }
    
    @media (max-width: 576px) {
        .footer-stats {
            flex-direction: column;
            align-items: center;
        }
        
        .stat-pill {
            width: 100%;
            max-width: 200px;
            justify-content: center;
        }
        
        .social-links {
            gap: 0.75rem;
        }
        
        .social-link {
            width: 40px;
            height: 40px;
        }
        
        .newsletter-section {
            padding: 1.5rem;
        }
        
        .newsletter-form .input-group {
            flex-direction: column;
        }
        
        .newsletter-input,
        .newsletter-btn {
            border-radius: 8px;
            width: 100%;
        }
        
        .newsletter-btn {
            margin-top: 0.5rem;
            justify-content: center;
        }
        
        .tech-badges {
            gap: 0.3rem;
        }
        
        .tech-badge {
            font-size: 0.7rem;
            padding: 0.2rem 0.5rem;
        }
    }

    @media (max-width: 768px) {
        .hero-section {
            min-height: 70vh;
            text-align: center;
            padding-bottom: 1rem !important; /* Add bottom padding to hero section */
        }
        
        .pricing-card.featured {
            transform: none;
        }
        
        /* Fix stats section spacing on mobile */
        .stats-section {
            padding: 1.5rem 0 !important; /* Further reduce from 2rem to 1.5rem */
            margin-top: 1rem; /* Add top margin for better separation from hero */
            margin-bottom: 1rem; /* Add bottom margin for better separation to about */
        }
        
        .stat-item {
            padding: 1rem 0.75rem; /* Further reduce padding inside stat items */
            margin-bottom: 1rem; /* Add some separation between rows */
        }
        
        .stat-item:last-child {
            margin-bottom: 0;
        }
        
        /* Adjust display text size on mobile */
        .stat-item .display-6 {
            font-size: 2.25rem; /* Further reduce font size for better mobile experience */
        }
        
        /* Reduce About section padding on mobile for better balance */
        .section-padding {
            padding: 3rem 0 !important; /* Reduce from 5rem to 3rem */
        }
        
        /* Adjust team section padding */
        .team-section {
            padding: 3rem 0 !important;
        }
        
        /* Adjust contact section padding */
        .contact-section {
            padding: 3rem 0 !important;
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

<!-- Enhanced Footer -->
<footer class="footer-enhanced">
    <div class="footer-gradient-overlay">
        <div class="container">
            <!-- Main Footer Content -->
            <div class="row footer-main">
                <!-- Brand Section -->
                <div class="col-lg-4 col-md-6 mb-5 mb-lg-0">
                    <div class="footer-brand">
                        <div class="brand-logo mb-3">
                            <div class="logo-icon">
                                <i class="ri-home-4-fill"></i>
                            </div>
                            <h3 class="brand-name">jhome</h3>
                        </div>
                        <p class="brand-description">
                            Revolutionizing property management with cutting-edge technology. 
                            Your complete solution for modern real estate professionals.
                        </p>
                        
                        <!-- Stats Pills -->
                        <div class="footer-stats mb-4">
                            <div class="stat-pill">
                                <i class="ri-building-line"></i>
                                <span>500+ Properties</span>
                            </div>
                            <div class="stat-pill">
                                <i class="ri-user-line"></i>
                                <span>50+ Clients</span>
                            </div>
                        </div>
                        
                        <!-- Social Media -->
                        <div class="social-links">
                            <a href="#" class="social-link facebook" aria-label="Facebook">
                                <i class="ri-facebook-fill"></i>
                            </a>
                            <a href="#" class="social-link twitter" aria-label="Twitter">
                                <i class="ri-twitter-fill"></i>
                            </a>
                            <a href="#" class="social-link linkedin" aria-label="LinkedIn">
                                <i class="ri-linkedin-fill"></i>
                            </a>
                            <a href="#" class="social-link instagram" aria-label="Instagram">
                                <i class="ri-instagram-fill"></i>
                            </a>
                            <a href="#" class="social-link github" aria-label="GitHub">
                                <i class="ri-github-fill"></i>
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Quick Links -->
                <div class="col-lg-2 col-md-3 col-6 mb-4 mb-lg-0">
                    <div class="footer-section">
                        <h6 class="footer-title">
                            <i class="ri-rocket-line me-2"></i>Product
                        </h6>
                        <ul class="footer-links">
                            <li><a href="#features">Features</a></li>
                            <li><a href="#pricing">Pricing Plans</a></li>
                            <li><a href="#about">About Platform</a></li>
                            <li><a href="/admin/login">Admin Login</a></li>
                            <li><a href="#" class="badge-link">
                                Free Trial <span class="badge">14 Days</span>
                            </a></li>
                        </ul>
                    </div>
                </div>
                
                <!-- Support -->
                <div class="col-lg-2 col-md-3 col-6 mb-4 mb-lg-0">
                    <div class="footer-section">
                        <h6 class="footer-title">
                            <i class="ri-customer-service-line me-2"></i>Support
                        </h6>
                        <ul class="footer-links">
                            <li><a href="#contact">Contact Us</a></li>
                            <li><a href="#" class="external-link">
                                Help Center <i class="ri-external-link-line"></i>
                            </a></li>
                            <li><a href="#" class="external-link">
                                Documentation <i class="ri-book-line"></i>
                            </a></li>
                            <li><a href="#" class="external-link">
                                API Docs <i class="ri-code-line"></i>
                            </a></li>
                            <li><a href="#" class="live-chat">
                                <i class="ri-chat-3-line me-1"></i>Live Chat
                                <span class="status-dot"></span>
                            </a></li>
                        </ul>
                    </div>
                </div>
                
                <!-- Company -->
                <div class="col-lg-2 col-md-3 col-6 mb-4 mb-lg-0">
                    <div class="footer-section">
                        <h6 class="footer-title">
                            <i class="ri-building-line me-2"></i>Company
                        </h6>
                        <ul class="footer-links">
                            <li><a href="#about">About Us</a></li>
                            <li><a href="#team">Our Team</a></li>
                            <li><a href="#">Careers <span class="hiring-badge">We're Hiring!</span></a></li>
                            <li><a href="#">Press Kit</a></li>
                            <li><a href="#contact">Contact</a></li>
                        </ul>
                    </div>
                </div>
                
                <!-- Contact Info -->
                <div class="col-lg-2 col-md-3 col-6">
                    <div class="footer-section">
                        <h6 class="footer-title">
                            <i class="ri-map-pin-line me-2"></i>Contact
                        </h6>
                        <div class="contact-info">
                            <div class="contact-item">
                                <i class="ri-phone-line"></i>
                                <div>
                                    <span class="contact-label">Phone</span>
                                    <a href="tel:+962612341234">+962 6 123 4567</a>
                                </div>
                            </div>
                            
                            <div class="contact-item">
                                <i class="ri-mail-line"></i>
                                <div>
                                    <span class="contact-label">Email</span>
                                    <a href="mailto:support@jhome.jo">support@jhome.jo</a>
                                </div>
                            </div>
                            
                            <div class="contact-item">
                                <i class="ri-map-pin-line"></i>
                                <div>
                                    <span class="contact-label">Address</span>
                                    <span>Amman, Jordan</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Legal Links -->
                        <div class="legal-links mt-3">
                            <a href="#" class="legal-link">Privacy Policy</a>
                            <a href="#" class="legal-link">Terms of Service</a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Newsletter Section -->
            <div class="newsletter-section">
                <div class="row align-items-center">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <h5 class="newsletter-title">
                            <i class="ri-mail-line me-2"></i>Stay Updated
                        </h5>
                        <p class="newsletter-text">Get the latest updates and property management tips</p>
                    </div>
                    <div class="col-md-6">
                        <div class="newsletter-form">
                            <div class="input-group">
                                <input type="email" class="form-control newsletter-input" 
                                       placeholder="Enter your email address">
                                <button class="btn newsletter-btn" type="button">
                                    <i class="ri-send-plane-line"></i>
                                    Subscribe
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Footer Bottom -->
            <div class="footer-bottom">
                <div class="row align-items-center">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <div class="copyright">
                            <p class="mb-0">
                                &copy; 2025 <span class="brand-highlight">jhome</span>. All rights reserved.
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="footer-meta">
                            <div class="made-with">
                                Made with <span class="heart">❤️</span> in <span class="jordan-flag">🇯🇴</span> Jordan
                            </div>
                            <div class="tech-badges">
                                <span class="tech-badge">Laravel</span>
                                <span class="tech-badge">Filament</span>
                                <span class="tech-badge">Bootstrap</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Decorative Elements -->
    <div class="footer-decoration">
        <div class="floating-elements">
            <div class="floating-element" style="--delay: 0s; --x: 10%; --y: 20%;">
                <i class="ri-home-4-line"></i>
            </div>
            <div class="floating-element" style="--delay: 2s; --x: 85%; --y: 30%;">
                <i class="ri-building-line"></i>
            </div>
            <div class="floating-element" style="--delay: 4s; --x: 25%; --y: 70%;">
                <i class="ri-key-line"></i>
            </div>
            <div class="floating-element" style="--delay: 6s; --x: 75%; --y: 80%;">
                <i class="ri-shield-check-line"></i>
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