<svg class="works__barrier" viewBox="0 0 300 190" aria-hidden="true">
    <defs>
        <pattern id="barrier-stripes" width="22" height="22" patternUnits="userSpaceOnUse" patternTransform="rotate(45)">
            <rect class="barrier__stripe" width="11" height="22"/>
        </pattern>
    </defs>

    <!-- pieds en A -->
    <g class="barrier__legs">
        <path d="M54 62 L34 180 M54 62 L74 180 M246 62 L226 180 M246 62 L266 180"/>
        <path d="M26 180 h56 M218 180 h56"/>
    </g>

    <!-- lisses rayées -->
    <rect class="barrier__plank" x="14" y="36" width="272" height="30" rx="4"/>
    <rect x="14" y="36" width="272" height="30" rx="4" fill="url(#barrier-stripes)"/>
    <rect class="barrier__outline" x="14" y="36" width="272" height="30" rx="4"/>

    <rect class="barrier__plank" x="40" y="90" width="220" height="16" rx="3"/>
    <rect x="40" y="90" width="220" height="16" rx="3" fill="url(#barrier-stripes)"/>
    <rect class="barrier__outline" x="40" y="90" width="220" height="16" rx="3"/>

    <!-- lampes de chantier, qui clignotent en alternance -->
    <g class="barrier__lamp">
        <rect x="41" y="28" width="16" height="9" rx="2"/>
        <circle class="barrier__bulb" cx="49" cy="20" r="9"/>
    </g>
    <g class="barrier__lamp barrier__lamp--late">
        <rect x="243" y="28" width="16" height="9" rx="2"/>
        <circle class="barrier__bulb" cx="251" cy="20" r="9"/>
    </g>

    <!-- panneau suspendu, qui se balance -->
    <g class="barrier__sign">
        <path class="barrier__chain" d="M124 106 L120 126 M176 106 L180 126"/>
        <rect x="92" y="126" width="116" height="36" rx="4"/>
        <text x="150" y="149" text-anchor="middle"><?= $this->t('EN TRAVAUX') ?></text>
    </g>
</svg>
