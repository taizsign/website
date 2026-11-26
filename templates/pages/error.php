<?php
/**
 * @var App\View\View $this
 * @var int $code
 * @var string $path
 * @var ?Throwable $error
 */
$host = 'raphael-jacquis.fr';
?>
<section class="page-head error">
    <div class="wrap">
        <?php if ($code === 404): ?>
            <pre class="trace" aria-hidden="true"><span>traceroute to <?= $this->e($host . $path) ?>, 30 hops max</span>
<span> 1  box.local              0.42 ms</span>
<span> 2  10.0.0.1               3.18 ms</span>
<span> 3  bes-core.fai.net       8.77 ms</span>
<span> 4  <?= $this->e(str_pad($host, 23)) ?>11.02 ms</span>
<span> 5  * * *</span>
<span> 6  * * *</span>
<span class="trace__end"> 7  <?= $this->t('destination introuvable') ?></span></pre>
            <p class="crumb"><?= $this->t('erreur {code}', ['code' => $code]) ?></p>
            <h1 class="h1"><?= $this->t('Paquet perdu.') ?></h1>
            <p class="lead lead--wide"><?= $this->t("L'adresse <code>{path}</code> ne mène nulle part. Le lien est peut-être ancien, ou la page a déménagé.", ['path' => $path]) ?></p>
        <?php else: ?>
            <p class="crumb"><?= $this->t('erreur {code}', ['code' => $code]) ?></p>
            <h1 class="h1"><?= $this->t('Le serveur a trébuché.') ?></h1>
            <p class="lead lead--wide"><?= $this->t("Une erreur s'est produite de mon côté. Elle est journalisée&nbsp;; réessayez dans un instant.") ?></p>
            <?php if (!empty($error)): ?>
                <pre class="code code--error"><?= $this->e(get_class($error) . ': ' . $error->getMessage() . "\n\n" . $error->getTraceAsString()) ?></pre>
            <?php endif; ?>
        <?php endif; ?>
        <div class="actions">
            <a class="btn" href="<?= $this->url('home') ?>"><?= $this->t("Retour à l'accueil") ?>
                <span class="arrow" aria-hidden="true"><?= $this->partial('partials/arrow') ?><?= $this->partial('partials/arrow') ?></span>
            </a>
            <a class="btn btn--ghost" href="<?= $this->url('projects') ?>"><?= $this->t('Voir les projets') ?></a>
        </div>
    </div>
</section>
