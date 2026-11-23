<?php

/*
 * Tech watch in English, same structure and order as ../watch.php.
 * Each entry: [title, body, meta]. Links stay in the French file.
 * Events: meta = "YEAR / MONTH / TAG"; the tag NOW marks the current event.
 */

return [
    'intro' => [
        ['Research question', 'How are generative AI tools changing **development practices** and the **developer\'s job**?'],
        [
            'Depending on infrastructure you don\'t control',
            'The most powerful AI tools run behind cloud subscriptions. Using Copilot or Claude means sending your code to servers you don\'t control.

In May 2023, Octave Klaba, founder of OVHcloud, wrote that he no longer wanted "to depend on licensed LLMs, with no control over the dataset", and committed to training and releasing an in-house model. In 2025, he told Maddyness he did not want an AI "fed 80% by data coming only from American culture".

Developers already have an alternative with **local LLMs**. Ollama runs open models such as Mistral or Qwen on your own machine. That leaves the practical questions of where the model runs, who sees the data and what hardware it takes.',
            'Source: X, May 2023',
        ],
    ],
    'axis' => [
        ['Coding assistants', 'Tools built into the editor that complete or rewrite code as you type. I follow how the models evolve, how they fit into IDEs, and the difference between autocompletion and talking to the tool.', 'copilot / cursor / jetbrains-ai'],
        ['Autonomous agents', 'Agents that chain the steps on their own. They read the code, change it, run the tests and open a pull request. I want to know how far you can let them go without reviewing everything.', 'claude-code / codex / devin'],
        ['Impact on the job', 'What it changes in real work. Productivity, code quality, the vulnerabilities it introduces, and what is now expected from a developer starting out.', 'productivity / security / code review'],
    ],
    'event' => [
        ['GitHub Copilot', 'The first mainstream coding assistant, built on OpenAI Codex.', '2021 / JUNE / LAUNCH'],
        ['ChatGPT opens to the public', 'Everyone starts asking a chatbot for code.', '2022 / NOVEMBER / TURNING POINT'],
        ['Devin, by Cognition Labs', 'Presented as the first "AI software engineer". People stop saying assistant and start saying agent.', '2024 / MARCH / AGENT'],
        ['GPT-4o', 'OpenAI\'s multimodal model, faster and better with code.', '2024 / MAY / MODEL'],
        ['Claude Code', 'A command-line agent that reads, edits and runs code directly on the developer\'s machine.', '2025 / FEBRUARY / AGENT'],
        ['Claude Opus 4.7', 'Stronger at coding, and it checks its own work before handing back.', '2026 / APRIL / MODEL'],
        ['GPT-5.5', 'OpenAI built it for agents more than for chat, with Codex in the front line.', '2026 / APRIL / MODEL'],
        ['GPT-6', 'A new generation at OpenAI, in several versions depending on price and power.', '2026 / SEPTEMBER / MODEL'],
        ['Claude Opus 5.5', 'The model I use with Claude Code to build this portfolio.', '2026 / SEPTEMBER / NOW'],
    ],
    'source' => [
        ['Anthropic News', 'Announcements about Claude and Claude Code, research on agents and AI safety.', 'anthropic.com/news'],
        ['GitHub Copilot documentation', 'Official documentation, new features and IDE integrations.', 'docs.github.com/fr/copilot'],
        ['Stack Overflow Developer Survey 2025', '84% of developers use AI or plan to. Yet 46% distrust what it produces, against 33% who trust it.', 'survey.stackoverflow.co/2025/ai'],
        ['IT-Connect', 'French-language tutorials and news on systems administration, networking and security.', 'it-connect.fr'],
    ],
    'insight' => [
        ['From assistant to agent', 'In 2021, Copilot finished a line. In 2026, an agent can go through a whole project, fix a bug and run the tests without anyone touching the keyboard. Every year, a bit more work gets handed to the machine.'],
        ['Reading code matters more than writing it', 'Producing code costs almost nothing now. What takes time is checking that it does what you want and doesn\'t open a hole. The Stack Overflow survey points the same way, almost everyone uses AI but many distrust its answers.'],
        ['Keeping control of your tools', 'Coding with a cloud service means sending your code to a company and depending on its prices. A local model with Ollama solves part of that, in exchange for a good graphics card and models a notch below.'],
    ],
];
