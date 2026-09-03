<?php
// Script to add missing keys to lang files
$additions = [
    'fr' => [
        'news' => "Actualités",
        'read_more' => "Lire la suite",
        'view_details' => "Voir les détails",
        'learn_more' => "En savoir plus",
        'sponsor_child' => "Parrainer un enfant",
        'hero_img_alt' => "Enfants parrainés",
        'active_projects' => "Projets actifs",
        'latest_news_title' => "Dernières actualités et mises à jour",
        'latest_news_subtitle' => "Voyez ce que nous faisons récemment sur le terrain.",
        'view_all_news' => "Voir toutes les actualités",
        'programs_home_subtitle' => "Un soutien holistique pour briser le cycle de la pauvreté.",
        'cta_title' => "Vous pouvez changer une vie aujourd'hui",
        'cta_text' => "Votre soutien aide les enfants à rester à l'école et les communautés à prospérer.",
        'news_updates' => "Actualités et mises à jour",
        'news_title' => "Dernières actualités et histoires d'impact",
        'news_subtitle' => "Restez informé du travail de FRISUCODE en Afrique de l'Est.",
        'all_categories' => "Toutes les catégories",
        'no_news_yet' => "Pas encore d'articles",
        'no_news_text' => "Revenez bientôt pour les mises à jour du terrain.",
        'news_fallback_title' => "Mise à jour du projet",
        'news_fallback_content' => "Notre initiative a atteint plus de 50 familles dans la région d'Arusha.",
        'share_update' => "Partager cette mise à jour",
        'share_text' => "Aidez-nous à diffuser le travail de FRISUCODE.",
        'back_to_news' => "Retour aux actualités",
        'more_updates' => "Plus de mises à jour",
        'more_updates_text' => "Continuez à lire nos dernières nouvelles.",
        'video_not_supported' => "Votre navigateur ne supporte pas la vidéo.",
        'subscribed_msg' => "Merci pour votre abonnement !",
    ],
    'de' => [
        'news' => "Neuigkeiten",
        'read_more' => "Mehr lesen",
        'view_details' => "Details anzeigen",
        'learn_more' => "Mehr erfahren",
        'sponsor_child' => "Kind fördern",
        'hero_img_alt' => "Geförderte Kinder",
        'active_projects' => "Aktive Projekte",
        'latest_news_title' => "Neueste Updates & Nachrichten",
        'latest_news_subtitle' => "Sehen Sie, was wir im Feld tun.",
        'view_all_news' => "Alle Neuigkeiten anzeigen",
        'programs_home_subtitle' => "Ganzheitliche Unterstützung zum Durchbrechen des Armutskreislaufs.",
        'cta_title' => "Sie können heute ein Leben verändern",
        'cta_text' => "Ihre Unterstützung hilft Kindern in der Schule zu bleiben und Gemeinden zu gedeihen.",
        'news_updates' => "Neuigkeiten & Updates",
        'news_title' => "Neueste Nachrichten & Wirkungsgeschichten",
        'news_subtitle' => "Bleiben Sie über die Arbeit von FRISUCODE in Ostafrika informiert.",
        'all_categories' => "Alle Kategorien",
        'no_news_yet' => "Noch keine Artikel",
        'no_news_text' => "Schauen Sie bald wieder vorbei.",
        'news_fallback_title' => "Projektupdate: Gemeinschaftsinitiative",
        'news_fallback_content' => "Unsere Initiative hat über 50 Familien in der Region Arusha erreicht.",
        'share_update' => "Dieses Update teilen",
        'share_text' => "Helfen Sie uns, die Arbeit von FRISUCODE zu verbreiten.",
        'back_to_news' => "Zurück zu den Neuigkeiten",
        'more_updates' => "Weitere Updates",
        'more_updates_text' => "Lesen Sie unsere neuesten Nachrichten.",
        'video_not_supported' => "Ihr Browser unterstützt das Video-Tag nicht.",
        'subscribed_msg' => "Vielen Dank für Ihr Abonnement!",
    ],
    'es' => [
        'news' => "Noticias",
        'read_more' => "Leer más",
        'view_details' => "Ver detalles",
        'learn_more' => "Saber más",
        'sponsor_child' => "Apadrinar un niño",
        'hero_img_alt' => "Niños apadrinados",
        'active_projects' => "Proyectos activos",
        'latest_news_title' => "Últimas noticias y actualizaciones",
        'latest_news_subtitle' => "Vea lo que estamos haciendo recientemente en el campo.",
        'view_all_news' => "Ver todas las noticias",
        'programs_home_subtitle' => "Apoyo integral para romper el ciclo de la pobreza.",
        'cta_title' => "Puedes cambiar una vida hoy",
        'cta_text' => "Tu apoyo ayuda a los niños a permanecer en la escuela y a las comunidades a prosperar.",
        'news_updates' => "Noticias y actualizaciones",
        'news_title' => "Últimas noticias e historias de impacto",
        'news_subtitle' => "Mantente informado sobre el trabajo de FRISUCODE en África Oriental.",
        'all_categories' => "Todas las categorías",
        'no_news_yet' => "Aún no hay artículos",
        'no_news_text' => "Vuelve pronto para ver actualizaciones del campo.",
        'news_fallback_title' => "Actualización del proyecto: Iniciativa comunitaria",
        'news_fallback_content' => "Nuestra iniciativa ha llegado a más de 50 familias en la región de Arusha.",
        'share_update' => "Compartir esta actualización",
        'share_text' => "Ayúdanos a difundir el trabajo de FRISUCODE.",
        'back_to_news' => "Volver a noticias",
        'more_updates' => "Más actualizaciones",
        'more_updates_text' => "Continúa leyendo nuestras últimas noticias.",
        'video_not_supported' => "Tu navegador no admite el elemento de video.",
        'subscribed_msg' => "¡Gracias por suscribirte!",
    ],
];

foreach ($additions as $lang => $keys) {
    $file = __DIR__ . "/../lang/{$lang}.php";
    $content = file_get_contents($file);
    
    // Remove closing ]; and add new keys before it
    $newKeys = "\n/* ========================= NEWS & HOME ADDITIONS ========================= */\n";
    foreach ($keys as $k => $v) {
        $escaped = str_replace("'", "\\'", $v);
        $newKeys .= "\"$k\" => \"$v\",\n";
    }
    
    $content = str_replace("];\n", $newKeys . "];\n", $content);
    // Handle windows line endings
    $content = str_replace("];\r\n", $newKeys . "];\r\n", $content);
    file_put_contents($file, $content);
    echo "Updated: $lang.php\n";
}

echo "Done.\n";
