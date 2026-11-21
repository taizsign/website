<?php

/*
 * Skills in English. Levels, statuses, domains and logos come from ../skills.php.
 *
 *  skills : French name => [English name, detail]
 *  proofs : free-text evidence, French => English (links to projects keep the project name)
 */

return [
    'skills' => [
        // Systems
        'Linux (Debian, Ubuntu)'            => ['Linux (Debian, Ubuntu)', 'Day-to-day administration: services, permissions, packages, logs.'],
        'PowerShell'                        => ['PowerShell', 'Automated workstation setup: installation and configuration in a single run.'],
        'Windows (poste client)'            => ['Windows (client)', 'Setting up, troubleshooting and tuning workstations.'],
        'Bash'                              => ['Bash', 'Operations and deployment scripts on the VPS.'],
        'Proxmox VE'                        => ['Proxmox VE', 'Virtual machines and LXC containers.'],
        'Docker'                            => ['Docker', 'Containerising my homelab services.'],
        'Cybersécurité'                     => ['Cybersecurity', 'Log and port analysis during my internship. OPSEC project, a workstation as secure as possible: risky chips removed, Qubes OS, VeraCrypt encryption, Mullvad VPN with a kill switch over two bridges, LibreWolf browser.'],
        'Windows Server · Active Directory' => ['Windows Server · Active Directory', 'Directory, GPOs, DNS and DHCP in a Windows environment.'],
        'Ansible'                           => ['Ansible', 'Describing my servers\' configuration instead of redoing it by hand.'],
        'Supervision (Zabbix, Grafana)'     => ['Monitoring (Zabbix, Grafana)', 'Knowing a service is down before a user tells me.'],
        'Sauvegarde et PRA'                 => ['Backup and disaster recovery', '3-2-1 rule, Proxmox Backup Server, restore tests.'],

        // Networks
        'Protocoles réseau'                 => ['Network protocols', 'TCP/IP, VLAN, DHCP, DNS, HTTP(S), SMTP, how TLS works.'],
        'Matériel et câblage'               => ['Hardware and cabling', 'Diagnostics, repair, SMD soldering, RJ45 crimping.'],
        'nginx'                             => ['nginx', 'Virtual hosts, PHP-FPM, several subdomains on a single VPS.'],
        'Pare-feu OPNsense'                 => ['OPNsense firewall', 'NAT, VLANs, filtering rules.'],
        'Routage et commutation (CCNA)'     => ['Routing and switching (CCNA)', 'Preparing for the CCNA: routing, switching, VLANs on Cisco hardware.'],
        'VPN WireGuard'                     => ['WireGuard VPN', 'Reaching the homelab from outside without opening needless ports.'],

        // Databases
        'Modélisation (MCD, MLD)'           => ['Data modelling (conceptual, logical)', 'Merise method, inheritance, cardinalities, mapping to the relational model.'],
        'MySQL'                             => ['MySQL', 'Joins, complex queries, triggers, creation scripts.'],
        'SQLite'                            => ['SQLite', 'Schema, views and CHECK constraints: it is the database behind this site.'],
        'Doctrine ORM'                      => ['Doctrine ORM', 'Entities, relations, QueryBuilder, migrations.'],
        'PostgreSQL'                        => ['PostgreSQL', 'The open source DBMS most in demand in companies.'],
        'Administration de SGBD'            => ['Database administration', 'Permissions, backup and restore, replication, index tuning.'],

        // Development
        'PHP'                               => ['PHP', 'PHP 8, OOP, PDO. This site is written in object-oriented PHP without a framework.'],
        'Symfony'                           => ['Symfony', 'Controllers, forms, Security, Twig.'],
        'C# · .NET'                         => ['C# · .NET', 'OOP, WinForms, MVC architecture, API calls.'],
        'API REST'                          => ['REST APIs', 'Route design, JSON, HTTP authentication.'],
        'Tests unitaires'                   => ['Unit testing', 'PHPUnit and MSTest.'],
        'Python'                            => ['Python', 'Automation scripts and file handling.'],
        'JavaScript'                        => ['JavaScript', 'Interactions and animations without a library.'],

        // Tools
        'Git · GitHub'                      => ['Git · GitHub', 'Branches, pull requests, code review.'],
        'CI/CD GitHub Actions'              => ['CI/CD with GitHub Actions', 'Automatic SSH deployment on every push.'],
        'Microsoft 365'                     => ['Microsoft 365', 'Word, Excel, Outlook and Teams every day, at school and during my internship.'],
        'Prompt engineering · Claude'       => ['Prompt engineering · Claude', 'Writing precise prompts, steering a coding agent, reviewing and testing what it produces.'],
        'Cloud Azure (AZ-900)'              => ['Azure cloud (AZ-900)', 'Public cloud basics first, then Azure administration.'],
    ],

    'proofs' => [
        'VPS OVH'               => 'OVH VPS',
        'Script setup-computer' => 'setup-computer script',
        'Stage technicien'      => 'Technician internship',
        'Épreuve E7'            => 'E7 exam',
        'Projet OPSEC'          => 'OPSEC project',
        'Atelier perso'         => 'My own workbench',
        'Ce portfolio'          => 'This portfolio',
        'Scripts perso'         => 'Personal scripts',
        'Veille technologique'  => 'Tech watch',
    ],
];
