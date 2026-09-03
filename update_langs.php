<?php
$keys = <<<EOT

/* =========================
   PARTNERS & REPORTS
========================= */
"nav_partners" => "Partners",
"nav_reports" => "Reports",
"partners_title" => "Partner With Us",
"partners_subtitle" => "Join us in transforming communities through strategic partnerships.",
"tier_bronze" => "Bronze Partner",
"tier_silver" => "Silver Partner",
"tier_gold" => "Gold Partner",
"why_partner" => "Why Partner With Us?",
"value_transparency" => "Radical Transparency",
"value_track_record" => "15+ Years Track Record",
"value_global" => "Global Reach",
"value_tax" => "Tax Deductibility",
"our_partners" => "Our Current Partners",
"sdg_alignment" => "Our Work & The SDGs",
"inquiry_form_title" => "Start Your Partnership Journey",
"reports_title" => "Transparency & Reports",
"reports_subtitle" => "Committed to accountability and data-driven impact.",
"annual_reports" => "Annual Impact Reports",
"download_report" => "Download Report",
"financial_transparency" => "Financial Transparency",
"fund_allocation" => "Fund Allocation",
"governance" => "Governance & Compliance",

EOT;

$dir = __DIR__ . "/lang";
$files = glob($dir . "/*.php");

foreach ($files as $file) {
    $content = file_get_contents($file);
    if (strpos($content, '"nav_partners"') === false) {
        $content = str_replace("];", $keys . "];", $content);
        file_put_contents($file, $content);
        echo "Updated " . basename($file) . "\n";
    } else {
        echo "Skipped " . basename($file) . "\n";
    }
}
?>
