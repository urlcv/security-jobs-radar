<?php

declare(strict_types=1);

namespace URLCV\SecurityJobsRadar\Laravel;

use App\Tools\Contracts\ToolInterface;

class SecurityJobsRadarTool implements ToolInterface
{
    public function slug(): string
    {
        return 'security-jobs-radar';
    }

    public function name(): string
    {
        return 'Security Jobs Radar';
    }

    public function summary(): string
    {
        return 'Enter a domain to see what a company\'s security hiring activity suggests about current priorities, tooling, or pressure points.';
    }

    public function descriptionMd(): ?string
    {
        return <<<'MD'
## Security Jobs Radar

Enter any company domain and we'll discover their careers page, find security-related job listings, and analyse the hiring signals.

### What you get

- **Security domains** — which areas the company is investing in (AppSec, Cloud Security, GRC, SOC/IR, DevSecOps, etc.)
- **Tools & platforms** — specific products mentioned in listings (Splunk, CrowdStrike, Snyk, Okta, etc.)
- **Compliance drivers** — frameworks and regulations driving hiring (SOC 2, ISO 27001, PCI DSS, HIPAA, GDPR, etc.)
- **Urgency signals** — whether hiring looks routine or reactive (growth phase, post-incident, audit pressure)
- **Team maturity** — is this a greenfield build-out or an established program scaling up?
- **Seniority map** — what levels are being hired (entry through CISO)

### Who it's for

- **Security professionals** — scope out a potential employer's security posture before interviewing
- **Sales teams** — understand a prospect's security priorities and pain points
- **Recruiters** — quickly map a company's security hiring landscape
- **Researchers** — track industry trends through hiring patterns

### How it works

We scrape the company's public careers page and detect common ATS platforms (Greenhouse, Lever, Ashby, Workable, etc.) to find job listings. Security-related roles are filtered and analysed against curated keyword dictionaries. No login required.
MD;
    }

    public function mode(): string
    {
        return 'frontend';
    }

    public function isAsync(): bool
    {
        return false;
    }

    public function isPublic(): bool
    {
        return true;
    }

    public function categories(): array
    {
        return ['security', 'recruiting'];
    }

    public function tags(): array
    {
        return ['security', 'hiring', 'jobs', 'intelligence', 'osint'];
    }

    public function inputSchema(): array
    {
        return [
            'domain' => [
                'type'        => 'string',
                'label'       => 'Company domain',
                'placeholder' => 'stripe.com',
                'required'    => true,
                'max_length'  => 255,
                'help'        => 'Enter the company\'s main domain (e.g. stripe.com).',
            ],
        ];
    }

    public function run(array $input): array
    {
        return [];
    }

    public function rateLimitPerMinute(): int
    {
        return 6;
    }

    public function cacheTtlSeconds(): int
    {
        return 0;
    }

    public function sortWeight(): int
    {
        return 90;
    }

    public function frontendView(): ?string
    {
        return 'security-jobs-radar::security-jobs-radar';
    }
}
