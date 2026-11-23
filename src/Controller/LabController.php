<?php

declare(strict_types=1);

namespace App\Controller;

use App\Domain\Topology;
use App\Http\Request;
use App\Http\Response;

final class LabController extends AbstractController
{
    public function index(Request $request): Response
    {
        $sections = $this->container->config['lab'];

        return $this->render('pages/lab', [
            'title'       => $this->t('Labo'),
            'description' => $this->t('Mon infrastructure en production et mes outils. Le homelab et le ping en direct vers mes serveurs arrivent bientôt.'),
            'showHomelab' => $sections['homelab'],
            'showProbes'  => $sections['probes'],
            'units'       => $sections['homelab'] ? $this->container->content()->labUnits() : [],
            // la sonde HTTP ne tourne que si la section est affichée
            'services'    => $sections['probes'] ? $this->container->uptime()->status() : [],
            'infra'       => $this->infrastructure(),
        ]);
    }

    /**
     * Ce qui tourne réellement sous taizsys.xyz.
     */
    private function infrastructure(): Topology
    {
        $t = $this->t(...);

        return new Topology(
            nodes: [
                'dns'     => ['id' => 'dns',     'x' => 30,  'y' => 40,  'w' => 190, 'label' => 'taizsys.xyz',     'sub' => $t('zone DNS'),                 'kind' => 'external'],
                'gh'      => ['id' => 'gh',      'x' => 30,  'y' => 300, 'w' => 190, 'label' => 'GitHub',          'sub' => $t('dépôts / Actions / Pages'), 'kind' => 'external'],
                'pages'   => ['id' => 'pages',   'x' => 300, 'y' => 300, 'w' => 200, 'label' => 'setup.taizsys.xyz', 'sub' => 'GitHub Pages / .ps1',     'kind' => 'service'],
                'nginx'   => ['id' => 'nginx',   'x' => 300, 'y' => 140, 'w' => 170, 'label' => 'nginx',           'sub' => $t('hôtes virtuels'),           'kind' => 'server'],
                'symfony' => ['id' => 'symfony', 'x' => 550, 'y' => 60,  'w' => 180, 'label' => 'Mediatek86',      'sub' => 'Symfony / PHP-FPM',        'kind' => 'service'],
                'docs'    => ['id' => 'docs',    'x' => 550, 'y' => 140, 'w' => 180, 'label' => 'Documentation',   'sub' => 'phpDocumentor',            'kind' => 'service'],
                'api'     => ['id' => 'api',     'x' => 550, 'y' => 220, 'w' => 180, 'label' => $t('API REST'),    'sub' => 'MediaTekDocuments',        'kind' => 'service'],
                'mysql'   => ['id' => 'mysql',   'x' => 790, 'y' => 140, 'w' => 145, 'label' => 'MySQL',           'sub' => $t('2 bases'),                  'kind' => 'data'],
            ],
            links: [
                ['from' => 'dns',     'to' => 'nginx',   'label' => $t('A / sous-domaines')],
                ['from' => 'nginx',   'to' => 'symfony', 'label' => '', 'flow' => true],
                ['from' => 'nginx',   'to' => 'docs',    'label' => '', 'flow' => true],
                ['from' => 'nginx',   'to' => 'api',     'label' => '', 'flow' => true],
                ['from' => 'symfony', 'to' => 'mysql',   'label' => '', 'flow' => true],
                ['from' => 'api',     'to' => 'mysql',   'label' => '', 'flow' => true],
                ['from' => 'gh',      'to' => 'nginx',   'label' => $t('déploiement SSH'), 'flow' => true],
                ['from' => 'gh',      'to' => 'pages',   'label' => $t('publie')],
            ],
            groups: [
                ['label' => $t('VPS OVH / Ubuntu'), 'x' => 280, 'y' => 20, 'w' => 670, 'h' => 290],
            ],
        );
    }
}
