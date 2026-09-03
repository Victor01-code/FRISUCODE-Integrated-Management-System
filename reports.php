<?php 
require_once __DIR__ . '/system/config/db.php';
include 'partials/header.php'; 
?>

<div class="page-hero">
    <h1><?= ($L['reports_title'] ?? 'Transparency & Reports') ?></h1>
    <p><?= ($L['reports_subtitle'] ?? 'Committed to accountability and data-driven impact.') ?></p>
</div>

<section style="padding: 80px 20px; max-width: 1200px; margin: auto;">
    <div class="stats" style="border: none; padding: 0 0 60px 0;">
        <div>
            <h2 style="font-size: 2.5rem; color: var(--primary);">#67812</h2>
            <p>NGO Registration Number</p>
        </div>
        <div>
            <h2 style="font-size: 2.5rem; color: #16a34a;">Audited</h2>
            <p>Financial Status</p>
        </div>
        <div>
            <h2 style="font-size: 2.5rem; color: #ea580c;">15+</h2>
            <p>Years Active</p>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 40px; margin-bottom: 80px;">
        <!-- Left: Annual Reports -->
        <div>
            <h2 style="font-size: 2.2rem; margin-bottom: 30px;"><?= ($L['annual_reports'] ?? 'Annual Impact Reports') ?></h2>
            
            <div style="display: flex; flex-direction: column; gap: 20px;">
                <div class="report-card">
                    <i class="fa-regular fa-file-pdf report-icon"></i>
                    <div class="report-content">
                        <h3>2025 Impact Report</h3>
                        <p>Detailed breakdown of our education and health initiatives.</p>
                        <a href="#" class="btn-download"><i class="fa-solid fa-download"></i> <?= ($L['download_report'] ?? 'Download Report') ?> (2.4 MB)</a>
                    </div>
                </div>
                
                <div class="report-card">
                    <i class="fa-regular fa-file-pdf report-icon"></i>
                    <div class="report-content">
                        <h3>2024 Impact Report</h3>
                        <p>Review of our infrastructure projects and community development.</p>
                        <a href="#" class="btn-download"><i class="fa-solid fa-download"></i> <?= ($L['download_report'] ?? 'Download Report') ?> (1.8 MB)</a>
                    </div>
                </div>
                
                <div class="report-card">
                    <i class="fa-regular fa-file-pdf report-icon" style="color: #94a3b8;"></i>
                    <div class="report-content">
                        <h3 style="color: #94a3b8;">2026 Impact Report</h3>
                        <p>Currently in compilation. Expected release: Feb 2027.</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Right: Financial Allocation -->
        <div style="background: #f8fafc; padding: 40px; border-radius: 20px;">
            <h2 style="font-size: 2.2rem; margin-bottom: 20px; text-align: center;"><?= ($L['fund_allocation'] ?? 'Fund Allocation') ?></h2>
            <p style="text-align: center; color: #64748b; margin-bottom: 40px;">How every dollar is maximized for impact.</p>
            
            <div class="pie-chart-container">
                <div class="pie-chart">
                    <div class="pie-chart-inner">
                        <h3 style="font-size: 2rem; color: var(--primary);">100%</h3>
                        <span style="font-size: 0.9rem; color: #64748b; font-weight: 600;">Accountability</span>
                    </div>
                </div>
                
                <div class="pie-legend">
                    <div class="legend-item">
                        <div class="legend-color" style="background: #2563eb;"></div>
                        60% Education
                    </div>
                    <div class="legend-item">
                        <div class="legend-color" style="background: #10b981;"></div>
                        20% Health
                    </div>
                    <div class="legend-item">
                        <div class="legend-color" style="background: #ea580c;"></div>
                        15% Community
                    </div>
                    <div class="legend-item">
                        <div class="legend-color" style="background: #64748b;"></div>
                        5% Operations
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section style="background: white; padding: 80px 20px; border-top: 1px solid #e2e8f0;">
    <div style="max-width: 1000px; margin: auto; text-align: center;">
        <h2 style="font-size: 2.2rem; margin-bottom: 20px;"><?= ($L['governance'] ?? 'Governance & Compliance') ?></h2>
        <p style="color: #64748b; margin-bottom: 50px; max-width: 700px; margin-left: auto; margin-right: auto;">FRISUCODE operates under strict compliance with the Non-Governmental Organizations Act, 2002 of the United Republic of Tanzania.</p>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 30px;">
            <div style="padding: 20px; border: 1px solid #e2e8f0; border-radius: 12px; background: #f8fafc;">
                <i class="fa-solid fa-scale-balanced" style="font-size: 2rem; color: var(--primary); margin-bottom: 15px;"></i>
                <h4 style="margin-bottom: 10px;">Board of Directors</h4>
                <p style="font-size: 0.9rem; color: #64748b;">Independent oversight board meeting quarterly.</p>
            </div>
            <div style="padding: 20px; border: 1px solid #e2e8f0; border-radius: 12px; background: #f8fafc;">
                <i class="fa-solid fa-file-signature" style="font-size: 2rem; color: var(--primary); margin-bottom: 15px;"></i>
                <h4 style="margin-bottom: 10px;">Annual Audits</h4>
                <p style="font-size: 0.9rem; color: #64748b;">External financial audits conducted by certified firms.</p>
            </div>
            <div style="padding: 20px; border: 1px solid #e2e8f0; border-radius: 12px; background: #f8fafc;">
                <i class="fa-solid fa-shield-halved" style="font-size: 2rem; color: var(--primary); margin-bottom: 15px;"></i>
                <h4 style="margin-bottom: 10px;">Data Protection</h4>
                <p style="font-size: 0.9rem; color: #64748b;">Strict beneficiary privacy and BCRYPT system security.</p>
            </div>
            <div style="padding: 20px; border: 1px solid #e2e8f0; border-radius: 12px; background: #f8fafc;">
                <i class="fa-solid fa-stamp" style="font-size: 2rem; color: var(--primary); margin-bottom: 15px;"></i>
                <h4 style="margin-bottom: 10px;">Govt Registered</h4>
                <p style="font-size: 0.9rem; color: #64748b;">Fully compliant with national regulatory bodies.</p>
            </div>
        </div>
    </div>
</section>

<?php include 'partials/footer.php'; ?>
