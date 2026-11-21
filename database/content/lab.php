<?php

/*
 * La baie du homelab, de haut en bas.
 *  hauteur : en U (1 à 4)
 *  statut  : en place | en cours | prévu
 */

return [
    [1, 'Pare-feu · routeur', 'OPNsense',              'en cours', 'NAT, découpage en VLAN, règles de filtrage entre le réseau de la maison et celui du labo.'],
    [1, 'Commutation',        'Switch manageable',     'prévu',    'Faire circuler les VLAN jusqu\'aux machines.'],
    [3, 'Hyperviseur',        'Proxmox VE',            'en cours', 'Machines virtuelles et conteneurs LXC : c\'est là que tout le reste tourne.'],
    [2, 'Stockage',           'NAS',                   'en cours', 'Stockage partagé et sauvegardes des machines virtuelles.'],
    [1, 'Conteneurs',         'Docker',                'en cours', 'Mes services du quotidien, isolés et faciles à reconstruire.'],
    [1, 'Supervision',        'Zabbix · Grafana',      'prévu',    'Tableaux de bord et alertes sur l\'état des machines et des services.'],
    [1, 'Annuaire',           'Windows Server · AD',   'prévu',    'Un vrai domaine Active Directory pour pratiquer GPO, DNS et DHCP.'],
    [1, 'Sauvegarde',         'Proxmox Backup Server', 'prévu',    'Sauvegardes incrémentales, et surtout des tests de restauration.'],
];
