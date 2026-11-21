<?php

/*
 * Veille technologique.
 *  intro   : [0] problématique, [1] sous-problématique (title, body, meta = libellé du lien, url)
 *  axis    : axes de recherche          (title, body, meta = mots-clés)
 *  event   : chronologie                (title, body, meta = « AAAA · MOIS · ÉTIQUETTE »)
 *  source  : sources suivies            (title, body, meta = adresse affichée, url)
 *  insight : synthèse                   (title, body)
 */

return [
    'intro' => [
        ['Problématique', "Comment les outils d'IA générative transforment-ils les **pratiques de développement** et le **métier de développeur** ?"],
        [
            "Dépendre d'une infrastructure qu'on ne contrôle pas",
            "Les outils d'IA les plus puissants passent par des abonnements cloud. Utiliser Copilot ou Claude, c'est envoyer son code sur des serveurs qu'on ne contrôle pas.

En mai 2023, Octave Klaba, fondateur d'OVHcloud, écrivait vouloir « ne plus dépendre de LLM sous licence, sans contrôle sur le dataset », et s'engageait à entraîner puis publier un modèle maison. En 2025, il déclarait à Maddyness ne pas vouloir d'une IA « alimentée par 80 % de données provenant uniquement de la culture américaine ».

Côté développeur, il existe déjà une alternative avec les **LLM locaux**. Ollama permet de faire tourner des modèles ouverts comme Mistral ou Qwen sur sa propre machine. Reste à savoir où tourne le modèle, qui voit les données et quel matériel il faut pour ça.",
            'Source : X, mai 2023',
            'https://x.com/olesovhcom/status/1659857562047479809',
        ],
    ],
    'axis' => [
        ['Assistants de code', "Les outils intégrés à l'éditeur qui complètent ou réécrivent le code pendant qu'on tape. Je suis l'évolution des modèles, leur intégration dans les IDE, et la différence entre l'autocomplétion et la discussion avec l'outil.", 'copilot · cursor · jetbrains-ai'],
        ['Agents autonomes', "Des agents qui enchaînent les étapes seuls. Ils lisent le code, le modifient, lancent les tests et ouvrent une pull request. Je cherche à savoir jusqu'où on peut les laisser faire sans tout relire.", 'claude-code · codex · devin'],
        ['Impact sur le métier', "Ce que ça change dans le travail réel. La productivité, la qualité du code, les failles introduites, et ce qu'on attend maintenant d'un développeur qui débute.", 'productivité · sécurité · relecture de code'],
    ],
    'event' => [
        ['GitHub Copilot', 'Premier assistant de code grand public, basé sur OpenAI Codex.', '2021 · JUIN · LANCEMENT'],
        ['ChatGPT ouvert au public', "Tout le monde se met à demander du code à un chatbot.", '2022 · NOVEMBRE · RUPTURE'],
        ['Devin, de Cognition Labs', "Présenté comme le premier « ingénieur logiciel IA ». On ne parle plus d'assistant mais d'agent.", '2024 · MARS · AGENT'],
        ['GPT-4o', "Modèle multimodal d'OpenAI, plus rapide et meilleur en code.", '2024 · MAI · MODÈLE'],
        ['Claude Code', "Un agent en ligne de commande qui lit, modifie et exécute le code directement sur la machine du développeur.", '2025 · FÉVRIER · AGENT'],
        ['Claude Opus 4.7', "Plus solide en code, et il vérifie son propre travail avant de rendre la main.", '2026 · AVRIL · MODÈLE'],
        ['GPT-5.5', "OpenAI le pense pour les agents plus que pour la conversation, avec Codex en première ligne.", '2026 · AVRIL · MODÈLE'],
        ['GPT-6', "Nouvelle génération chez OpenAI, en plusieurs versions selon le prix et la puissance.", '2026 · SEPTEMBRE · MODÈLE'],
        ['Claude Opus 5.5', "Le modèle que j'utilise avec Claude Code pour développer ce portfolio.", '2026 · SEPTEMBRE · EN COURS'],
    ],
    'source' => [
        ['Anthropic News', "Annonces autour de Claude et de Claude Code, recherche sur les agents et la sécurité de l'IA.", 'anthropic.com/news', 'https://www.anthropic.com/news'],
        ['Documentation GitHub Copilot', 'Documentation officielle en français, nouveautés et intégrations aux IDE.', 'docs.github.com/fr/copilot', 'https://docs.github.com/fr/copilot'],
        ['Stack Overflow Developer Survey 2025', "84 % des développeurs utilisent l'IA ou comptent le faire. Pourtant 46 % se méfient de ce qu'elle produit, contre 33 % qui lui font confiance.", 'survey.stackoverflow.co/2025/ai', 'https://survey.stackoverflow.co/2025/ai'],
        ['IT-Connect', "Tutoriels et actualités en français sur l'administration systèmes, le réseau et la sécurité.", 'it-connect.fr', 'https://www.it-connect.fr/'],
    ],
    'insight' => [
        ["De l'assistant à l'agent", "En 2021, Copilot complétait une ligne. En 2026, un agent peut parcourir tout un projet, corriger un bug et lancer les tests sans qu'on touche au clavier. Chaque année, on confie un peu plus de travail à la machine."],
        ['Savoir relire compte plus que savoir écrire', "Produire du code ne coûte presque plus rien. Ce qui prend du temps, c'est de vérifier qu'il fait ce qu'on veut et qu'il n'ouvre pas de faille. L'enquête Stack Overflow va dans ce sens, presque tout le monde utilise l'IA mais beaucoup se méfient de ses réponses."],
        ['Garder la main sur ses outils', "Coder avec un service cloud, c'est envoyer son code à une entreprise et dépendre de ses prix. Un modèle local avec Ollama règle une partie du problème, en échange d'une bonne carte graphique et de modèles un cran en dessous."],
    ],
];
