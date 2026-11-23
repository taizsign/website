<?php

/*
 * Homelab rack in English, in the same order as ../lab.php: [role, name, note].
 * Height and status stay in the French file.
 */

return [
    ['Firewall / router', 'OPNsense',              'NAT, VLAN segmentation, filtering rules between the home network and the lab.'],
    ['Switching',         'Managed switch',        'Carrying the VLANs all the way to the machines.'],
    ['Hypervisor',        'Proxmox VE',            'Virtual machines and LXC containers: everything else runs here.'],
    ['Storage',           'NAS',                   'Shared storage and virtual machine backups.'],
    ['Containers',        'Docker',                'My everyday services, isolated and easy to rebuild.'],
    ['Monitoring',        'Zabbix / Grafana',      'Dashboards and alerts on the state of machines and services.'],
    ['Directory',         'Windows Server / AD',   'A real Active Directory domain to practise GPOs, DNS and DHCP.'],
    ['Backup',            'Proxmox Backup Server', 'Incremental backups, and above all restore tests.'],
];
