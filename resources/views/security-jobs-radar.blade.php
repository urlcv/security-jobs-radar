{{-- Security Jobs Radar — domain-based scanner with server-side scraping --}}

<div x-data="securityJobsRadar()" class="space-y-6">

    {{-- Intro --}}
    <div class="rounded-2xl border border-gray-200 bg-white p-5">
        <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-primary-700">Security Hiring Intelligence</p>
        <h2 class="mt-2 text-xl font-semibold text-gray-900">See what a company's security hiring says about their priorities</h2>
        <p class="mt-2 text-sm leading-6 text-gray-600">
            Enter a company domain and we'll scrape their careers page to find security-related job listings, then analyse them for tooling, compliance focus, urgency signals, and team maturity.
        </p>

        <button
            type="button"
            class="mt-3 text-xs text-gray-500 underline hover:text-gray-700"
            @click="showLimitations = !showLimitations"
            x-text="showLimitations ? 'Hide limitations' : 'Show limitations'"
        ></button>
        <div x-show="showLimitations" x-cloak x-transition class="mt-3 rounded-xl border border-blue-200 bg-blue-50 p-4 text-sm text-blue-900">
            <ul class="list-disc pl-5 space-y-1">
                <li>Only scrapes the <strong>initial HTML</strong> of careers pages — JS-rendered content (SPAs) may be missed.</li>
                <li>Detects common ATS platforms (Greenhouse, Lever, Ashby, Workable, etc.) but some companies use custom systems.</li>
                <li>Analysis is <strong>keyword-based</strong> — it reads job titles and page text, not the full JD body from every listing.</li>
                <li>Results reflect <strong>current listings only</strong>, not historical trends.</li>
            </ul>
        </div>

        <div class="mt-4 grid gap-3 md:grid-cols-3">
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-3">
                <div class="text-xs font-semibold uppercase tracking-wide text-emerald-700">What you get</div>
                <p class="mt-1 text-sm text-emerald-900">A breakdown of security priorities, tooling, compliance drivers, urgency signals, and team maturity based on public job listings.</p>
            </div>
            <div class="rounded-xl border border-amber-200 bg-amber-50 p-3">
                <div class="text-xs font-semibold uppercase tracking-wide text-amber-700">Why it matters</div>
                <p class="mt-1 text-sm text-amber-900">Job listings are one of the best public signals for understanding a company's security posture and investment priorities.</p>
            </div>
            <div class="rounded-xl border border-blue-200 bg-blue-50 p-3">
                <div class="text-xs font-semibold uppercase tracking-wide text-blue-700">Who it's for</div>
                <p class="mt-1 text-sm text-blue-900">Security professionals, sales teams, recruiters, and researchers tracking industry hiring trends.</p>
            </div>
        </div>
    </div>

    {{-- Input --}}
    <div class="rounded-2xl border border-gray-200 bg-white p-5 space-y-4">
        <div class="text-sm font-semibold text-gray-900">Company domain</div>
        <form @submit.prevent="runScan" class="flex flex-col gap-3 sm:flex-row sm:items-start">
            <div class="flex-1">
                <input
                    type="text"
                    class="block w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                    placeholder="stripe.com"
                    x-model="domain"
                    :disabled="busy"
                    required
                >
                <p class="mt-1 text-xs text-gray-400">Enter the company's main domain. We'll find their careers page automatically.</p>
            </div>
            <button
                type="submit"
                class="inline-flex items-center justify-center rounded-lg bg-primary-600 px-5 py-2.5 text-sm font-medium text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed shrink-0"
                :disabled="busy || !domain.trim()"
            >
                <template x-if="busy">
                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                </template>
                <span x-text="busy ? 'Scanning\u2026' : 'Scan Hiring Activity'"></span>
            </button>
        </form>

        <div class="flex flex-wrap gap-2">
            <span class="text-xs text-gray-400">Try:</span>
            <button type="button" class="rounded-lg border border-gray-200 bg-gray-50 px-3 py-1 text-xs text-gray-600 hover:bg-gray-100" @click="domain = 'gitlab.com'; runScan()">gitlab.com</button>
            <button type="button" class="rounded-lg border border-gray-200 bg-gray-50 px-3 py-1 text-xs text-gray-600 hover:bg-gray-100" @click="domain = 'datadog.com'; runScan()">datadog.com</button>
            <button type="button" class="rounded-lg border border-gray-200 bg-gray-50 px-3 py-1 text-xs text-gray-600 hover:bg-gray-100" @click="domain = 'stripe.com'; runScan()">stripe.com</button>
        </div>
    </div>

    {{-- Error --}}
    <template x-if="lastError">
        <div class="rounded-2xl border border-red-200 bg-red-50 p-5">
            <div class="flex items-start gap-3">
                <svg class="h-5 w-5 text-red-500 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                <div>
                    <div class="text-sm font-medium text-red-800">Scan failed</div>
                    <p class="mt-1 text-sm text-red-700" x-text="lastError"></p>
                </div>
            </div>
        </div>
    </template>

    {{-- Loading --}}
    <template x-if="busy">
        <div class="rounded-2xl border border-gray-200 bg-white p-8 text-center">
            <svg class="animate-spin h-8 w-8 text-primary-500 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
            </svg>
            <p class="mt-3 text-sm text-gray-600">Discovering careers pages and scanning job listings&hellip;</p>
            <p class="mt-1 text-xs text-gray-400">This usually takes 10&ndash;30 seconds depending on the site.</p>
        </div>
    </template>

    {{-- Results --}}
    <template x-if="result && !busy">
        <div class="space-y-5">

            {{-- Intel Brief --}}
            <div class="rounded-2xl border p-5" :class="result.security_jobs_found > 0 ? 'border-primary-200 bg-primary-50' : 'border-gray-200 bg-gray-50'">
                <div class="flex items-start gap-3">
                    <div class="text-2xl shrink-0" x-text="result.security_jobs_found > 0 ? '\uD83D\uDD0D' : '\uD83D\uDCAD'"></div>
                    <div class="flex-1">
                        <div class="text-base font-semibold text-gray-900">Intel Brief</div>
                        <p class="mt-1 text-sm leading-6 text-gray-700" x-text="result.intel_brief"></p>
                        <div class="mt-3 flex flex-wrap gap-4 text-xs text-gray-500">
                            <span><strong x-text="result.total_jobs_found"></strong> total jobs found</span>
                            <span><strong x-text="result.security_jobs_found"></strong> security-related</span>
                            <a :href="result.careers_url" target="_blank" rel="noopener" class="text-primary-600 hover:text-primary-700 underline" x-text="'Careers page'"></a>
                            <span x-show="result.ats_detected" class="inline-flex items-center px-2 py-0.5 rounded-full bg-gray-100 text-gray-600 text-[10px] font-medium">
                                via <span x-text="result.ats_detected" class="ml-0.5"></span>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Security Job Titles --}}
            <div x-show="result.security_job_titles.length > 0" class="rounded-2xl border border-gray-200 bg-white p-5">
                <h3 class="text-sm font-semibold text-gray-900 mb-3">Security Roles Found</h3>
                <div class="flex flex-wrap gap-2">
                    <template x-for="title in result.security_job_titles" :key="title">
                        <span class="inline-flex items-center px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-700" x-text="title"></span>
                    </template>
                </div>
            </div>

            {{-- Security Domains --}}
            <div x-show="Object.keys(result.domains).length > 0" class="rounded-2xl border border-gray-200 bg-white p-5">
                <h3 class="text-sm font-semibold text-gray-900 mb-4">Security Focus Areas</h3>
                <div class="space-y-3">
                    <template x-for="[domain, data] in sortedDomains()" :key="domain">
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-sm text-gray-700 font-medium" x-text="domain"></span>
                                <span class="text-xs text-gray-400" x-text="data.score + ' signals'"></span>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-2">
                                <div class="bg-primary-500 h-2 rounded-full transition-all duration-500" :style="'width: ' + domainBarWidth(data.score) + '%'"></div>
                            </div>
                            <div class="mt-1 flex flex-wrap gap-1">
                                <template x-for="term in data.terms.slice(0, 8)" :key="term">
                                    <span class="text-[10px] px-1.5 py-0.5 rounded bg-primary-50 text-primary-700 border border-primary-100" x-text="term"></span>
                                </template>
                                <span x-show="data.terms.length > 8" class="text-[10px] px-1.5 py-0.5 text-gray-400" x-text="'+' + (data.terms.length - 8) + ' more'"></span>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Tools & Platforms --}}
            <div x-show="Object.keys(result.tools).length > 0" class="rounded-2xl border border-gray-200 bg-white p-5">
                <h3 class="text-sm font-semibold text-gray-900 mb-4">Tools & Platforms Mentioned</h3>
                <div class="space-y-3">
                    <template x-for="[category, products] in Object.entries(result.tools)" :key="category">
                        <div>
                            <div class="text-xs font-semibold uppercase tracking-wide text-gray-500 mb-1.5" x-text="category"></div>
                            <div class="flex flex-wrap gap-1.5">
                                <template x-for="product in products" :key="product">
                                    <span class="inline-flex items-center px-2.5 py-1 bg-violet-50 border border-violet-200 rounded-lg text-xs font-medium text-violet-700" x-text="product"></span>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Compliance Frameworks --}}
            <div x-show="result.compliance.length > 0" class="rounded-2xl border border-gray-200 bg-white p-5">
                <h3 class="text-sm font-semibold text-gray-900 mb-3">Compliance Drivers</h3>
                <div class="flex flex-wrap gap-2">
                    <template x-for="fw in result.compliance" :key="fw.name">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-50 border border-amber-200 rounded-lg text-sm font-medium text-amber-800">
                            <span x-text="fw.name"></span>
                            <span class="text-[10px] text-amber-500 font-normal" x-text="fw.mentions > 1 ? '\u00d7' + fw.mentions : ''"></span>
                        </span>
                    </template>
                </div>
            </div>

            {{-- Urgency + Maturity + Seniority grid --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                {{-- Urgency --}}
                <div class="rounded-2xl border p-5" :class="urgencyStyle()">
                    <div class="text-xs font-semibold uppercase tracking-wide mb-2" :class="urgencyLabelColor()">Urgency Level</div>
                    <div class="text-lg font-bold capitalize" x-text="result.urgency.level === 'unknown' ? 'Normal' : result.urgency.level"></div>
                    <div x-show="result.urgency.signals.length > 0" class="mt-2 flex flex-wrap gap-1">
                        <template x-for="s in result.urgency.signals" :key="s">
                            <span class="text-[10px] px-1.5 py-0.5 rounded bg-white/60 border border-current/10" x-text="s"></span>
                        </template>
                    </div>
                    <p x-show="result.urgency.signals.length === 0" class="mt-1 text-xs opacity-70">No strong urgency signals detected.</p>
                </div>

                {{-- Maturity --}}
                <div class="rounded-2xl border border-gray-200 bg-white p-5">
                    <div class="text-xs font-semibold uppercase tracking-wide text-gray-500 mb-2">Team Maturity</div>
                    <div class="text-lg font-bold capitalize" x-text="result.maturity.stage === 'unknown' ? 'Unclear' : result.maturity.stage"></div>
                    <div x-show="result.maturity.signals.length > 0" class="mt-2 flex flex-wrap gap-1">
                        <template x-for="s in result.maturity.signals" :key="s">
                            <span class="text-[10px] px-1.5 py-0.5 rounded bg-gray-100 text-gray-600" x-text="s"></span>
                        </template>
                    </div>
                    <p x-show="result.maturity.signals.length === 0" class="mt-1 text-xs text-gray-400">Not enough signals to determine maturity.</p>
                </div>

                {{-- Seniority --}}
                <div class="rounded-2xl border border-gray-200 bg-white p-5">
                    <div class="text-xs font-semibold uppercase tracking-wide text-gray-500 mb-2">Seniority Map</div>
                    <template x-if="Object.keys(result.seniority).length > 0">
                        <div class="space-y-1.5">
                            <template x-for="[level, count] in Object.entries(result.seniority)" :key="level">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-700" x-text="level"></span>
                                    <span class="text-sm font-semibold text-gray-900" x-text="count"></span>
                                </div>
                            </template>
                        </div>
                    </template>
                    <p x-show="Object.keys(result.seniority).length === 0" class="text-xs text-gray-400 mt-1">No seniority data available.</p>
                </div>
            </div>

            {{-- Export --}}
            <div class="flex flex-wrap gap-2">
                <button type="button" @click="copyReport()"
                    class="px-4 py-2.5 rounded-xl text-sm font-medium transition-colors"
                    :class="copied ? 'bg-emerald-50 border border-emerald-300 text-emerald-700' : 'bg-primary-600 text-white hover:bg-primary-700'">
                    <span x-show="!copied">Copy report</span>
                    <span x-show="copied" x-cloak>Copied!</span>
                </button>
                <button type="button" @click="downloadReport()"
                    class="px-4 py-2.5 rounded-xl text-sm font-medium border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 transition-colors">
                    Download JSON
                </button>
            </div>

            {{-- Footer --}}
            <div class="rounded-xl border border-dashed border-gray-200 bg-gray-50 p-4 text-center text-xs text-gray-400">
                Scanned <span x-text="result.domain"></span> &middot;
                <span x-text="result.total_jobs_found + ' total jobs'"></span> &middot;
                <span x-text="result.security_jobs_found + ' security role' + (result.security_jobs_found !== 1 ? 's' : '')"></span>
            </div>
        </div>
    </template>
</div>

@push('scripts')
<script>
function securityJobsRadar() {
    return {
        domain: '',
        busy: false,
        lastError: null,
        result: null,
        copied: false,
        showLimitations: false,

        async runScan() {
            const raw = this.domain.trim();
            if (!raw) return;

            let domain = raw.replace(/^https?:\/\//i, '').replace(/\/.*$/, '').toLowerCase();
            if (!domain || !domain.includes('.')) {
                this.lastError = 'Please enter a valid domain (e.g. stripe.com).';
                return;
            }

            this.domain = domain;
            this.busy = true;
            this.lastError = null;
            this.result = null;
            this.copied = false;

            try {
                const response = await fetch('/tools/security-jobs-radar-scan', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    },
                    body: JSON.stringify({ domain }),
                });

                const data = await response.json();

                if (!response.ok) {
                    this.lastError = data.error || 'Something went wrong. Please try again.';
                    return;
                }

                this.result = data;
            } catch (e) {
                this.lastError = 'Network error \u2014 could not reach the scan endpoint. Please try again.';
            } finally {
                this.busy = false;
            }
        },

        sortedDomains() {
            if (!this.result) return [];
            return Object.entries(this.result.domains).sort((a, b) => b[1].score - a[1].score);
        },

        domainBarWidth(score) {
            if (!this.result) return 0;
            const max = Math.max(...Object.values(this.result.domains).map(d => d.score), 1);
            return Math.max(8, Math.round((score / max) * 100));
        },

        urgencyStyle() {
            if (!this.result) return 'border-gray-200 bg-white';
            const map = {
                high: 'border-red-200 bg-red-50',
                moderate: 'border-amber-200 bg-amber-50',
                low: 'border-emerald-200 bg-emerald-50',
                unknown: 'border-gray-200 bg-gray-50',
            };
            return map[this.result.urgency.level] || map.unknown;
        },

        urgencyLabelColor() {
            if (!this.result) return 'text-gray-500';
            const map = {
                high: 'text-red-700',
                moderate: 'text-amber-700',
                low: 'text-emerald-700',
                unknown: 'text-gray-500',
            };
            return map[this.result.urgency.level] || map.unknown;
        },

        copyReport() {
            if (!this.result) return;
            const text = this.buildTextReport();
            navigator.clipboard.writeText(text).then(() => {
                this.copied = true;
                setTimeout(() => { this.copied = false; }, 2000);
            });
        },

        downloadReport() {
            if (!this.result) return;
            const blob = new Blob([JSON.stringify(this.result, null, 2)], { type: 'application/json' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'security-jobs-radar-' + this.result.domain + '.json';
            a.click();
            URL.revokeObjectURL(url);
        },

        buildTextReport() {
            const r = this.result;
            let out = `Security Jobs Radar Report\n`;
            out += `${'='.repeat(50)}\n`;
            out += `Domain: ${r.domain}\n`;
            out += `Careers URL: ${r.careers_url}\n`;
            out += `Total jobs: ${r.total_jobs_found}\n`;
            out += `Security roles: ${r.security_jobs_found}\n\n`;

            out += `INTEL BRIEF\n${'-'.repeat(30)}\n${r.intel_brief}\n\n`;

            if (r.security_job_titles.length > 0) {
                out += `SECURITY ROLES\n${'-'.repeat(30)}\n`;
                r.security_job_titles.forEach(t => out += `  - ${t}\n`);
                out += '\n';
            }

            const domainEntries = Object.entries(r.domains);
            if (domainEntries.length > 0) {
                out += `SECURITY FOCUS AREAS\n${'-'.repeat(30)}\n`;
                domainEntries.sort((a, b) => b[1].score - a[1].score);
                domainEntries.forEach(([name, data]) => {
                    out += `  ${name} (${data.score} signals): ${data.terms.join(', ')}\n`;
                });
                out += '\n';
            }

            const toolEntries = Object.entries(r.tools);
            if (toolEntries.length > 0) {
                out += `TOOLS & PLATFORMS\n${'-'.repeat(30)}\n`;
                toolEntries.forEach(([cat, products]) => {
                    out += `  ${cat}: ${products.join(', ')}\n`;
                });
                out += '\n';
            }

            if (r.compliance.length > 0) {
                out += `COMPLIANCE DRIVERS\n${'-'.repeat(30)}\n`;
                r.compliance.forEach(fw => out += `  ${fw.name} (\u00d7${fw.mentions})\n`);
                out += '\n';
            }

            out += `URGENCY: ${r.urgency.level}`;
            if (r.urgency.signals.length > 0) out += ` (${r.urgency.signals.join(', ')})`;
            out += '\n';

            out += `MATURITY: ${r.maturity.stage}`;
            if (r.maturity.signals.length > 0) out += ` (${r.maturity.signals.join(', ')})`;
            out += '\n\n';

            const seniorityEntries = Object.entries(r.seniority);
            if (seniorityEntries.length > 0) {
                out += `SENIORITY MAP\n${'-'.repeat(30)}\n`;
                seniorityEntries.forEach(([level, count]) => out += `  ${level}: ${count}\n`);
                out += '\n';
            }

            out += `${'='.repeat(50)}\n`;
            out += `Scanned with https://urlcv.com/tools/security-jobs-radar\n`;
            return out;
        },
    };
}
</script>
@endpush
