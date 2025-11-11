@php
    $finalStatus = '';
    $badgeClass = '';

    // Priorité 1: Archivé (status = 2)
    if ($client->status == 2) {
        $finalStatus = 'Archivé';
        $badgeClass = 'dark';
    }
    // Priorité 2: Compte de Test (is_demo = 2)
    elseif ($client->is_demo == 2) {
        $finalStatus = 'Test';
        $badgeClass = 'warning';
    }
    // Priorité 3: Compte en Démo (is_demo = 1)
    elseif ($client->is_demo == 1) {
        $finalStatus = 'En Démo';
        $badgeClass = 'info';
    }
    // Priorité 4: Statut basé sur le paiement (uniquement si ce n'est ni Démo, ni Test)
    elseif ($client->lastPayment) {
        if ($client->lastPayment->status == 1) { // Payé
            $finalStatus = 'Payé';
            $badgeClass = 'success';
        } elseif ($client->lastPayment->status == 2) { // Expiré
            $finalStatus = 'Expiré';
            $badgeClass = 'danger';
        } else { // En vérification
             $finalStatus = 'Payé'; // Comme demandé, on considère "En vérification" comme Payé
             $badgeClass = 'success';
        }
    }
    // Cas par défaut (rare, si aucune des conditions ci-dessus n'est remplie)
    else {
        $finalStatus = 'Payé';
        $badgeClass = 'success';
    }
@endphp

<span class="badge text-light-{{ $badgeClass }}">{{ $finalStatus }}</span>
