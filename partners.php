<?php 
require_once __DIR__ . '/system/config/db.php';
include 'partials/header.php'; 
?>

<div class="page-hero">
    <h1><?= ($L['partners_title'] ?? 'Partner With Us') ?></h1>
    <p><?= ($L['partners_subtitle'] ?? 'Join us in transforming communities through strategic partnerships.') ?></p>
</div>

<section style="padding: 80px 20px; max-width: 1200px; margin: auto;">
    <div style="text-align: center; margin-bottom: 50px;">
        <h2 style="font-size: 2.5rem; margin-bottom: 20px;">Partnership Opportunities</h2>
        <p style="color: #64748b; font-size: 1.1rem; max-width: 700px; margin: auto;">Choose how you want to make an impact. We offer transparent, structured partnership tiers for organizations and foundations.</p>
    </div>

    <div class="partner-tiers">
        <div class="tier-card">
            <i class="fa-solid fa-medal tier-icon" style="color: #cd7f32;"></i>
            <h3><?= ($L['tier_bronze'] ?? 'Bronze Partner') ?></h3>
            <div class="tier-price">$5,000<span>/year</span></div>
            <p style="color: #64748b; margin-bottom: 20px;">Ideal for small businesses or individuals looking to start their CSR journey.</p>
            <ul class="tier-features">
                <li><i class="fa-solid fa-check"></i> Sponsors 10 students</li>
                <li><i class="fa-solid fa-check"></i> Quarterly Impact Report</li>
                <li><i class="fa-solid fa-check"></i> Logo on Website</li>
            </ul>
        </div>
        
        <div class="tier-card featured">
            <div class="tier-badge">RECOMMENDED</div>
            <i class="fa-solid fa-medal tier-icon" style="color: #94a3b8;"></i>
            <h3><?= ($L['tier_silver'] ?? 'Silver Partner') ?></h3>
            <div class="tier-price">$15,000<span>/year</span></div>
            <p style="color: #64748b; margin-bottom: 20px;">Great for mid-sized companies aiming for a community-level impact.</p>
            <ul class="tier-features">
                <li><i class="fa-solid fa-check"></i> Sponsors 35 students</li>
                <li><i class="fa-solid fa-check"></i> Dedicated Project Visit</li>
                <li><i class="fa-solid fa-check"></i> Co-branded Marketing</li>
                <li><i class="fa-solid fa-check"></i> Monthly Updates</li>
            </ul>
        </div>
        
        <div class="tier-card">
            <i class="fa-solid fa-medal tier-icon" style="color: #fbbf24;"></i>
            <h3><?= ($L['tier_gold'] ?? 'Gold Partner') ?></h3>
            <div class="tier-price">$50,000+<span>/year</span></div>
            <p style="color: #64748b; margin-bottom: 20px;">For large corporations & foundations driving systemic change.</p>
            <ul class="tier-features">
                <li><i class="fa-solid fa-check"></i> Funds Infrastructure Projects</li>
                <li><i class="fa-solid fa-check"></i> Executive Board Updates</li>
                <li><i class="fa-solid fa-check"></i> Custom Impact Dashboard</li>
                <li><i class="fa-solid fa-check"></i> Naming Rights Opportunity</li>
            </ul>
        </div>
    </div>
</section>

<section style="background: #f8fafc; padding: 80px 20px;">
    <div style="max-width: 1200px; margin: auto;">
        <h2 style="text-align: center; font-size: 2.2rem; margin-bottom: 50px;"><?= ($L['why_partner'] ?? 'Why Partner With Us?') ?></h2>
        
        <div class="value-props">
            <div class="value-card">
                <i class="fa-solid fa-magnifying-glass-chart"></i>
                <h3><?= ($L['value_transparency'] ?? 'Radical Transparency') ?></h3>
                <p style="color: #64748b; margin-top: 10px;">Our custom management system ensures you know exactly where your funds are going, down to the last dollar.</p>
            </div>
            <div class="value-card">
                <i class="fa-solid fa-timeline"></i>
                <h3><?= ($L['value_track_record'] ?? '15+ Years Track Record') ?></h3>
                <p style="color: #64748b; margin-top: 10px;">Registered Tanzanian NGO (#67812) with over a decade of proven impact in the Arusha region.</p>
            </div>
            <div class="value-card">
                <i class="fa-solid fa-earth-africa"></i>
                <h3><?= ($L['value_global'] ?? 'Global Reach') ?></h3>
                <p style="color: #64748b; margin-top: 10px;">We currently work with over 450 donors globally and offer support in 5 languages.</p>
            </div>
            <div class="value-card">
                <i class="fa-solid fa-file-invoice-dollar"></i>
                <h3><?= ($L['value_tax'] ?? 'Tax Deductibility') ?></h3>
                <p style="color: #64748b; margin-top: 10px;">Eligible partnerships can benefit from structured tax deductions through our international channels.</p>
            </div>
        </div>
    </div>
</section>

<section style="padding: 80px 20px; max-width: 1200px; margin: auto; text-align: center;">
    <h2 style="font-size: 2.2rem; margin-bottom: 20px;"><?= ($L['sdg_alignment'] ?? 'Our Work & The SDGs') ?></h2>
    <p style="color: #64748b; margin-bottom: 40px; max-width: 700px; margin-left: auto; margin-right: auto;">Our programs directly contribute to the United Nations Sustainable Development Goals.</p>
    
    <div class="sdg-grid">
        <div class="sdg-item">
            <img src="https://sdgs.un.org/sites/default/files/goals/E_SDG_Icons-01.jpg" alt="No Poverty">
            <h4 style="font-size: 0.9rem;">No Poverty</h4>
        </div>
        <div class="sdg-item">
            <img src="https://sdgs.un.org/sites/default/files/goals/E_SDG_Icons-03.jpg" alt="Good Health">
            <h4 style="font-size: 0.9rem;">Good Health</h4>
        </div>
        <div class="sdg-item">
            <img src="https://sdgs.un.org/sites/default/files/goals/E_SDG_Icons-04.jpg" alt="Quality Education">
            <h4 style="font-size: 0.9rem;">Quality Education</h4>
        </div>
        <div class="sdg-item">
            <img src="https://sdgs.un.org/sites/default/files/goals/E_SDG_Icons-05.jpg" alt="Gender Equality">
            <h4 style="font-size: 0.9rem;">Gender Equality</h4>
        </div>
    </div>
</section>

<section style="background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%); color: white; padding: 80px 20px;">
    <div style="max-width: 800px; margin: auto; background: white; color: var(--text-main); padding: 40px; border-radius: 20px; box-shadow: 0 20px 40px rgba(0,0,0,0.2);">
        <h2 style="text-align: center; margin-bottom: 30px;"><?= ($L['inquiry_form_title'] ?? 'Start Your Partnership Journey') ?></h2>
        <form action="#" method="POST">
            <div class="cause-grid">
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" placeholder="Jane Doe" required>
                </div>
                <div class="form-group">
                    <label>Organization / Company</label>
                    <input type="text" placeholder="Acme Foundation" required>
                </div>
            </div>
            <div class="cause-grid">
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" placeholder="jane@example.com" required>
                </div>
                <div class="form-group">
                    <label>Interest Area</label>
                    <select required>
                        <option value="">Select an area...</option>
                        <option value="Education">Education Sponsorship</option>
                        <option value="Health">Healthcare Initiatives</option>
                        <option value="Infrastructure">Infrastructure Projects</option>
                        <option value="General">General Partnership</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label>Message</label>
                <textarea rows="4" placeholder="Tell us how you'd like to collaborate..." required></textarea>
            </div>
            <button type="submit" class="btn-primary" style="width: 100%; justify-content: center; font-size: 1.1rem; padding: 15px;">Submit Inquiry</button>
        </form>
    </div>
</section>

<?php include 'partials/footer.php'; ?>
