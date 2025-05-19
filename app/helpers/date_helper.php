<?php
/**
 * Fonction pour formater une date
 * @param string $date La date à formater
 * @param string $format Le format de sortie (par défaut: d/m/Y)
 * @return string La date formatée
 */
function formatDate($date, $format = 'd/m/Y') {
    if (empty($date)) {
        return '';
    }
    
    $timestamp = strtotime($date);
    if ($timestamp === false) {
        return $date;
    }
    
    return date($format, $timestamp);
}

/**
 * Fonction pour formater une date avec l'heure
 * @param string $date La date à formater
 * @return string La date formatée avec l'heure
 */
function formatDateTime($date) {
    return formatDate($date, 'd/m/Y H:i');
}

/**
 * Fonction pour formater une date en format relatif (ex: "il y a 2 jours")
 * @param string $date La date à formater
 * @return string La date au format relatif
 */
function formatRelativeDate($date) {
    if (empty($date)) {
        return '';
    }
    
    $timestamp = strtotime($date);
    if ($timestamp === false) {
        return $date;
    }
    
    $now = time();
    $diff = $now - $timestamp;
    
    if ($diff < 60) {
        return 'à l\'instant';
    } elseif ($diff < 3600) {
        $minutes = floor($diff / 60);
        return 'il y a ' . $minutes . ' minute' . ($minutes > 1 ? 's' : '');
    } elseif ($diff < 86400) {
        $hours = floor($diff / 3600);
        return 'il y a ' . $hours . ' heure' . ($hours > 1 ? 's' : '');
    } elseif ($diff < 604800) {
        $days = floor($diff / 86400);
        return 'il y a ' . $days . ' jour' . ($days > 1 ? 's' : '');
    } else {
        return formatDate($date);
    }
} 