<?php

namespace Database\Seeders;

use App\Models\CommunityEvent;
use App\Models\CommunityNotification;
use App\Models\CommunityPost;
use App\Models\Bookmark;
use App\Models\Conversation;
use App\Models\PostComment;
use App\Models\PostLike;
use App\Models\Project;
use App\Models\JobListing;
use App\Models\Message;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class CommunitySeeder extends Seeder
{
    public function run(): void
    {
        $users = collect([
            [
                'name' => 'Roeun Vireak',
                'username' => 'roeunvireak',
                'email' => 'chanvireak906@gmail.com',
                'password' => Hash::make('password'),
                'headline' => 'Senior DevOps officer and full-stack Laravel engineer',
                'location' => 'Phnom Penh',
                'bio' => 'Senior DevOps Officer with hands-on experience across Laravel APIs, AWS infrastructure, Vue and Quasar integration, CI/CD, and team mentoring.',
                'avatar_url' => 'https://ui-avatars.com/api/?name=Roeun+Vireak&background=0f172a&color=f8fafc',
                'company' => 'The Institute of Banking and Finance',
                'skills' => ['Laravel', 'Vue', 'Quasar', 'AWS', 'MySQL', 'Docker', 'GitHub Actions'],
                'availability' => 'Open for full-stack, backend, and DevOps opportunities',
                'portfolio_headline' => 'Building secure Laravel systems, cloud infrastructure, and practical internal platforms',
                'portfolio_summary' => 'I build and maintain full-stack systems with Laravel, Vue, and Quasar, with a strong focus on RESTful APIs, cloud deployment, database integrity, and production reliability.',
                'portfolio_plan' => 'premium',
                'portfolio_cover' => 'Senior DevOps officer shipping Laravel, AWS, and Quasar-based products',
                'portfolio_booking_url' => null,
                'portfolio_case_studies' => [
                    [
                        'title' => 'Internal CMS and Task Management Platforms',
                        'summary' => 'Built and maintained internal systems with Laravel, Vue.js, and role-based access control.',
                        'impact' => 'Improved daily operations through API integration, cleaner backend logic, and responsive interfaces.',
                        'link' => 'https://khdev.community/projects/internal-systems',
                    ],
                    [
                        'title' => 'Coffee Shop Management System',
                        'summary' => 'Developed and maintained backend features for customer data, orders, and reports using Laravel and MySQL.',
                        'impact' => 'Delivered assigned features with the project manager and improved backend reliability across modules.',
                        'link' => 'https://khdev.community/projects/coffee-shop-management',
                    ],
                ],
                'portfolio_testimonials' => [
                    [
                        'name' => 'Chhay Chenda',
                        'role' => 'Coordinator, Passerelles Numeriques Cambodia',
                        'quote' => 'Vireak is dependable, practical, and strong at turning requirements into maintainable systems.',
                    ],
                    [
                        'name' => 'Sokhom Hean',
                        'role' => 'Director, ANT Training Center',
                        'quote' => 'He combines teaching, mentoring, and production engineering in a way that helps teams grow quickly.',
                    ],
                ],
                'work_experience' => [
                    [
                        'role' => 'Senior DevOps Officer',
                        'company' => 'The Institute of Banking and Finance',
                        'period' => '08/2025 - Present',
                        'location' => 'Phnom Penh',
                        'type' => 'Full-time',
                        'summary' => 'Leads backend development with Laravel, manages AWS services, integrates Vue/Quasar with backend systems, and maintains API security, performance, and deployment stability.',
                    ],
                    [
                        'role' => 'Web Developer and Teacher',
                        'company' => 'ANT Training Center',
                        'period' => '03/2024 - 08/2025',
                        'location' => 'Phnom Penh',
                        'type' => 'Full-time',
                        'summary' => 'Developed full-stack applications using Laravel, Vue.js, and Nuxt.js, built CMS/task/farm/social systems, and taught PHP, MySQL, Laravel, and OOP short courses.',
                    ],
                    [
                        'role' => 'Laravel Developer',
                        'company' => 'Vichea IT Solutions',
                        'period' => '12/2023 - 02/2024',
                        'location' => 'Phnom Penh',
                        'type' => 'Full-time',
                        'summary' => 'Maintained backend features for the Coffee Shop Management System and worked closely with frontend teammates to deliver features and fix issues.',
                    ],
                ],
                'education_history' => [
                    [
                        'school' => 'Passerelles Numeriques Cambodia',
                        'degree' => 'Computer Science',
                        'field' => 'Practical software engineering',
                        'period' => '2025 - 2027',
                        'summary' => 'Ongoing advanced study focused on practical engineering and team-based product delivery.',
                    ],
                    [
                        'school' => 'Asia Europe University',
                        'degree' => 'Bachelor of Computer Science',
                        'field' => 'Computer Science',
                        'period' => '2021 - 2023',
                        'summary' => 'Built a strong foundation in programming, backend architecture, and web systems.',
                    ],
                ],
                'certifications' => [
                    [
                        'name' => 'Short Course Instructor - PHP, MySQL, Laravel, OOP',
                        'issuer' => 'Ministry of Post and Telecommunications',
                        'issued_at' => '2025',
                        'credential_url' => null,
                    ],
                    [
                        'name' => 'Professional English / Workplace Communication',
                        'issuer' => 'Passerelles Numeriques Cambodia',
                        'issued_at' => '2025',
                        'credential_url' => null,
                    ],
                ],
                'achievements' => [
                    [
                        'title' => 'Backend and Cloud Systems Lead',
                        'issuer' => 'The Institute of Banking and Finance',
                        'year' => '2025',
                        'summary' => 'Led backend development, AWS infrastructure, API security, and production issue resolution for core institutional systems.',
                    ],
                    [
                        'title' => 'Technical Mentor and Instructor',
                        'issuer' => 'ANT Training Center',
                        'year' => '2024',
                        'summary' => 'Mentored junior developers through code reviews, debugging support, and practical Laravel instruction.',
                    ],
                ],
                'social_links' => [
                    'github' => 'https://github.com/roeunvireak',
                    'linkedin' => 'https://linkedin.com/in/roeunvireak',
                    'portfolio' => 'https://khdev.community/@roeunvireak',
                    'x' => null,
                ],
                'featured_work' => [
                    [
                        'title' => 'Social Media Platform',
                        'description' => 'Built backend and frontend features for a social-style platform with API integration and role-based access control.',
                        'link' => 'https://khdev.community/projects/social-platform',
                        'stack' => 'Laravel, Vue.js, Quasar, MySQL',
                    ],
                    [
                        'title' => 'Chill Realm',
                        'description' => 'Collaborated on a YouTube-style platform, handling backend and frontend work as part of a team.',
                        'link' => 'https://khdev.community/projects/chill-realm',
                        'stack' => 'Laravel, JavaScript, MySQL',
                    ],
                ],
                'profile_palette' => [
                    'primary' => '#2563eb',
                    'secondary' => '#14b8a6',
                    'surface' => '#18212f',
                ],
            ],
            [
                'name' => 'Chan Ravy',
                'username' => 'chanravy',
                'email' => 'ravy@khdev.community',
                'password' => Hash::make('password'),
                'headline' => 'Frontend engineer shipping polished community products',
                'location' => 'Siem Reap',
                'bio' => 'Focuses on accessible interfaces, design systems, and community storytelling.',
                'avatar_url' => 'https://ui-avatars.com/api/?name=Chan+Ravy&background=1d4ed8&color=f8fafc',
                'company' => 'Temple Stack',
                'skills' => ['Vue', 'Tailwind', 'Design Systems'],
                'availability' => 'Available for product design sprints',
                'portfolio_headline' => 'Design systems and interfaces that feel local, modern, and sharp',
                'portfolio_summary' => 'I work on UI systems, community products, and content-heavy platforms that need strong hierarchy and personality.',
                'portfolio_plan' => 'premium',
                'portfolio_cover' => 'Designing Khmer product systems with sharper editorial structure',
                'portfolio_booking_url' => 'https://cal.com/chanravy',
                'portfolio_case_studies' => [
                    [
                        'title' => 'Kram Design System',
                        'summary' => 'Created a reusable component language for Khmer SaaS and content platforms.',
                        'impact' => 'Reduced UI inconsistency and improved handoff between design and engineering.',
                        'link' => 'https://khdev.community/projects/kram-design-system',
                    ],
                ],
                'portfolio_testimonials' => [
                    [
                        'name' => 'Roeun Vireak',
                        'role' => 'Senior DevOps Officer, The Institute of Banking and Finance',
                        'quote' => 'Ravy consistently gives products a stronger identity and better reading flow.',
                    ],
                ],
                'work_experience' => [
                    [
                        'role' => 'Senior Product Designer',
                        'company' => 'Temple Stack',
                        'period' => '2023 - Present',
                        'location' => 'Siem Reap',
                        'type' => 'Full-time',
                        'summary' => 'Owns interface systems, product hierarchy, and community-facing surfaces across web and mobile.',
                    ],
                    [
                        'role' => 'UI Engineer',
                        'company' => 'Studio Kram',
                        'period' => '2020 - 2023',
                        'location' => 'Remote',
                        'type' => 'Contract',
                        'summary' => 'Built design systems, landing pages, and editorial products for startups in Southeast Asia.',
                    ],
                ],
                'education_history' => [
                    [
                        'school' => 'Institute of Technology of Cambodia',
                        'degree' => 'Bachelor of Information and Communication Engineering',
                        'field' => 'Human-centered interfaces',
                        'period' => '2016 - 2020',
                        'summary' => 'Combined interface design with frontend implementation and product communication.',
                    ],
                ],
                'certifications' => [
                    [
                        'name' => 'Google UX Design Certificate',
                        'issuer' => 'Google',
                        'issued_at' => '2022',
                        'credential_url' => 'https://grow.google/certificates/',
                    ],
                ],
                'achievements' => [
                    [
                        'title' => 'Design System Lead',
                        'issuer' => 'Khmer Dev Community',
                        'year' => '2025',
                        'summary' => 'Defined the visual system that unified feed, portfolio, and event experiences.',
                    ],
                ],
                'social_links' => [
                    'github' => 'https://github.com/chanravy',
                    'linkedin' => 'https://linkedin.com/in/chanravy',
                    'portfolio' => 'https://khdev.community/@chanravy',
                    'x' => 'https://x.com/chanravy',
                ],
                'featured_work' => [
                    [
                        'title' => 'Kram Design System',
                        'description' => 'A reusable design language for Khmer SaaS teams.',
                        'link' => 'https://khdev.community/projects/kram-design-system',
                        'stack' => 'Vue, Tailwind, Storybook',
                    ],
                ],
                'profile_palette' => [
                    'primary' => '#fb7185',
                    'secondary' => '#38bdf8',
                    'surface' => '#172033',
                ],
            ],
            [
                'name' => 'Lim Vicheka',
                'username' => 'limvicheka',
                'email' => 'vicheka@khdev.community',
                'password' => Hash::make('password'),
                'headline' => 'Mobile builder growing Flutter circles in Cambodia',
                'location' => 'Battambang',
                'bio' => 'Runs mobile sprints, product demos, and async feedback sessions.',
                'avatar_url' => 'https://ui-avatars.com/api/?name=Lim+Vicheka&background=0f766e&color=f8fafc',
                'company' => 'Mekong Apps',
                'skills' => ['Flutter', 'Firebase', 'Product'],
                'availability' => 'Open to mobile product partnerships',
                'portfolio_headline' => 'Shipping mobile tools that are simple, useful, and fast to learn',
                'portfolio_summary' => 'I help teams move from idea to shipped mobile product through product thinking, feedback loops, and practical engineering.',
                'portfolio_plan' => 'free',
                'social_links' => [
                    'github' => 'https://github.com/limvicheka',
                    'linkedin' => 'https://linkedin.com/in/limvicheka',
                    'portfolio' => 'https://khdev.community/@limvicheka',
                    'x' => 'https://x.com/limvicheka',
                ],
                'featured_work' => [
                    [
                        'title' => 'Flutter Ship Room',
                        'description' => 'A mobile product critique and release support program for Khmer builders.',
                        'link' => 'https://khdev.community/events/flutter-ship-room',
                        'stack' => 'Flutter, Firebase',
                    ],
                ],
                'profile_palette' => [
                    'primary' => '#14b8a6',
                    'secondary' => '#22d3ee',
                    'surface' => '#12232a',
                ],
            ],
            [
                'name' => 'Khim Nita',
                'username' => 'khimnita',
                'email' => 'nita@khdev.community',
                'password' => Hash::make('password'),
                'headline' => 'DevOps advocate helping teams deploy with confidence',
                'location' => 'Phnom Penh',
                'bio' => 'Teaches CI/CD, observability, and sustainable release workflows.',
                'avatar_url' => 'https://ui-avatars.com/api/?name=Khim+Nita&background=7c2d12&color=f8fafc',
                'company' => 'Cloud Khmer',
                'skills' => ['DevOps', 'Docker', 'AWS'],
                'availability' => 'Available for DevOps office hours',
                'portfolio_headline' => 'Helping Khmer teams deploy with confidence and fewer surprises',
                'portfolio_summary' => 'My work focuses on CI/CD, release safety, observability, and giving smaller teams strong production habits.',
                'portfolio_plan' => 'free',
                'social_links' => [
                    'github' => 'https://github.com/khimnita',
                    'linkedin' => 'https://linkedin.com/in/khimnita',
                    'portfolio' => 'https://khdev.community/@khimnita',
                    'x' => 'https://x.com/khimnita',
                ],
                'featured_work' => [
                    [
                        'title' => 'Deploy Kampuchea',
                        'description' => 'A practical deployment starter platform for Khmer SaaS teams.',
                        'link' => 'https://khdev.community/projects/deploy-kampuchea',
                        'stack' => 'Docker, GitHub Actions, AWS',
                    ],
                ],
                'profile_palette' => [
                    'primary' => '#f59e0b',
                    'secondary' => '#f97316',
                    'surface' => '#261815',
                ],
            ],
            [
                'name' => 'Pich Sreypov',
                'username' => 'pichsreypov',
                'email' => 'sreypov@khdev.community',
                'password' => Hash::make('password'),
                'headline' => 'Data engineer turning community metrics into product decisions',
                'location' => 'Phnom Penh',
                'bio' => 'Builds analytics pipelines, community dashboards, and ranking systems for product teams.',
                'avatar_url' => 'https://ui-avatars.com/api/?name=Pich+Sreypov&background=6d28d9&color=f8fafc',
                'company' => 'Insight Mekong',
                'skills' => ['Python', 'Analytics', 'ETL'],
                'availability' => 'Open to analytics consulting',
                'portfolio_headline' => 'Turning product signals into decisions that communities can act on',
                'portfolio_summary' => 'I build dashboards, metrics pipelines, and reporting loops that help teams see what matters and move faster.',
                'portfolio_plan' => 'free',
                'social_links' => [
                    'github' => 'https://github.com/pichsreypov',
                    'linkedin' => 'https://linkedin.com/in/pichsreypov',
                    'portfolio' => 'https://khdev.community/@pichsreypov',
                    'x' => 'https://x.com/pichsreypov',
                ],
                'featured_work' => [
                    [
                        'title' => 'KDC Insight Board',
                        'description' => 'Analytics views for feed quality and builder retention.',
                        'link' => 'https://khdev.community/projects/kdc-insight-board',
                        'stack' => 'Python, FastAPI, Supabase',
                    ],
                ],
                'profile_palette' => [
                    'primary' => '#8b5cf6',
                    'secondary' => '#ec4899',
                    'surface' => '#221b35',
                ],
            ],
            [
                'name' => 'Ros Makara',
                'username' => 'rosmakara',
                'email' => 'makara@khdev.community',
                'password' => Hash::make('password'),
                'headline' => 'Full-stack maker shipping community tools for students',
                'location' => 'Kampot',
                'bio' => 'Works on learning products, open-source starter kits, and practical onboarding flows.',
                'avatar_url' => 'https://ui-avatars.com/api/?name=Ros+Makara&background=be123c&color=f8fafc',
                'company' => 'Build Camp',
                'skills' => ['Nuxt', 'Laravel', 'Product Design'],
                'availability' => 'Open to startup and education collaborations',
                'portfolio_headline' => 'Starter kits and product systems for builders learning by shipping',
                'portfolio_summary' => 'I create templates, onboarding flows, and product experiences that help newer developers reach production faster.',
                'portfolio_plan' => 'free',
                'social_links' => [
                    'github' => 'https://github.com/rosmakara',
                    'linkedin' => 'https://linkedin.com/in/rosmakara',
                    'portfolio' => 'https://khdev.community/@rosmakara',
                    'x' => 'https://x.com/rosmakara',
                ],
                'featured_work' => [
                    [
                        'title' => 'HackKit Khmer',
                        'description' => 'A hackathon-ready starter kit for community teams and students.',
                        'link' => 'https://khdev.community/projects/hackkit-khmer',
                        'stack' => 'Nuxt, Laravel, SQLite',
                    ],
                ],
                'profile_palette' => [
                    'primary' => '#ef4444',
                    'secondary' => '#f97316',
                    'surface' => '#2b1620',
                ],
            ],
        ])->map(fn (array $user) => User::updateOrCreate(['email' => $user['email']], $user));

        $now = Carbon::now();

        foreach ([
            [
                'user_id' => $users[0]->id,
                'title' => 'Launching Khmer Dev Community v1 with Laravel and Tailwind',
                'slug' => 'launching-khmer-dev-community-v1',
                'topic' => 'Laravel',
                'excerpt' => 'A practical blueprint for combining feed, projects, events, and mobile surfaces in one product.',
                'body' => 'We are shipping the first community stack with a home for builders, meetups, and collaboration.',
                'reading_time' => 4,
                'likes_count' => 94,
                'comments_count' => 18,
                'pinned' => true,
                'published_at' => $now->copy()->subHours(3),
            ],
            [
                'user_id' => $users[1]->id,
                'title' => 'Designing a feed that feels familiar to GitHub, Dev.to, and Discord users',
                'slug' => 'designing-a-familiar-feed',
                'topic' => 'Frontend',
                'excerpt' => 'Navigation density, readable cards, and live sidebars matter more than decoration.',
                'body' => 'The visual system uses strong panels, high contrast, and clean information hierarchy.',
                'reading_time' => 5,
                'likes_count' => 67,
                'comments_count' => 9,
                'pinned' => false,
                'published_at' => $now->copy()->subHours(7),
            ],
            [
                'user_id' => $users[2]->id,
                'title' => 'How we turn web community data into mobile-first surfaces',
                'slug' => 'web-data-into-mobile-surfaces',
                'topic' => 'Mobile',
                'excerpt' => 'Same community graph, different interaction model: concise actions, fast scans, clearer state.',
                'body' => 'Mobile members need quick feed, notifications, profile, and messages without extra noise.',
                'reading_time' => 3,
                'likes_count' => 52,
                'comments_count' => 6,
                'pinned' => false,
                'published_at' => $now->copy()->subDay(),
            ],
            [
                'user_id' => $users[4]->id,
                'title' => 'Tracking community health with a weekly engineering dashboard',
                'slug' => 'tracking-community-health-dashboard',
                'topic' => 'Data',
                'excerpt' => 'We built a simple dashboard for posts, active builders, event registrations, and contributor velocity.',
                'body' => 'A good community product needs visible signals, not vanity charts. This setup keeps the team focused on actions.',
                'reading_time' => 4,
                'likes_count' => 44,
                'comments_count' => 5,
                'pinned' => false,
                'published_at' => $now->copy()->subDays(2),
            ],
            [
                'user_id' => $users[5]->id,
                'title' => 'Shipping a starter kit for Khmer student hackathon teams',
                'slug' => 'starter-kit-for-khmer-student-hackathons',
                'topic' => 'Open Source',
                'excerpt' => 'A practical template with auth, team boards, deployment presets, and project submission flows.',
                'body' => 'Students should spend time on ideas and execution, not on repeating the same bootstrap work every weekend.',
                'reading_time' => 6,
                'likes_count' => 73,
                'comments_count' => 14,
                'pinned' => false,
                'published_at' => $now->copy()->subDays(3),
            ],
            [
                'user_id' => $users[1]->id,
                'title' => 'Why the project directory needs stronger screenshots and changelogs',
                'slug' => 'project-directory-screenshots-and-changelogs',
                'topic' => 'UX',
                'excerpt' => 'Projects get more collaboration when the story is obvious: what changed, who it helps, and what is needed next.',
                'body' => 'We are adding more visual proof, contributor asks, and milestone formatting to project cards.',
                'reading_time' => 4,
                'likes_count' => 38,
                'comments_count' => 4,
                'pinned' => false,
                'published_at' => $now->copy()->subDays(4),
            ],
            [
                'user_id' => $users[3]->id,
                'title' => 'A production checklist for Khmer teams deploying their first SaaS',
                'slug' => 'production-checklist-first-saas',
                'topic' => 'DevOps',
                'excerpt' => 'Backups, health checks, logs, queues, queues again, and a rollback plan before the launch tweet.',
                'body' => 'The checklist is short, opinionated, and meant to prevent the most common first-launch failures.',
                'reading_time' => 7,
                'likes_count' => 81,
                'comments_count' => 11,
                'pinned' => false,
                'published_at' => $now->copy()->subDays(5),
            ],
        ] as $post) {
            CommunityPost::updateOrCreate(['slug' => $post['slug']], $post);
        }

        foreach ([
            [
                'user_id' => $users[0]->id,
                'name' => 'KhmerJobs API',
                'slug' => 'khmerjobs-api',
                'tagline' => 'An open API for Khmer-friendly engineering jobs.',
                'summary' => 'Indexes local and remote roles, salary ranges, and company stacks for the community feed.',
                'repo_url' => 'https://github.com/example/khmerjobs-api',
                'demo_url' => 'https://khdev.community/projects/khmerjobs-api',
                'tech_stack' => ['Laravel', 'Passport', 'PostgreSQL'],
                'contributors_count' => 8,
                'stars_count' => 182,
                'status' => 'active',
                'looking_for_collaborators' => true,
                'launched_at' => $now->copy()->subMonths(2)->toDateString(),
            ],
            [
                'user_id' => $users[1]->id,
                'name' => 'Kram Design System',
                'slug' => 'kram-design-system',
                'tagline' => 'Reusable UI primitives for Khmer SaaS teams.',
                'summary' => 'A component library and documentation hub for admin products and content platforms.',
                'repo_url' => 'https://github.com/example/kram-design-system',
                'demo_url' => 'https://khdev.community/projects/kram-design-system',
                'tech_stack' => ['Vue', 'Tailwind', 'Storybook'],
                'contributors_count' => 5,
                'stars_count' => 149,
                'status' => 'beta',
                'looking_for_collaborators' => true,
                'launched_at' => $now->copy()->subMonth()->toDateString(),
            ],
            [
                'user_id' => $users[3]->id,
                'name' => 'Deploy Kampuchea',
                'slug' => 'deploy-kampuchea',
                'tagline' => 'A starter platform for CI/CD and observability.',
                'summary' => 'Standardizes releases, monitoring, and rollback playbooks for small development teams.',
                'repo_url' => 'https://github.com/example/deploy-kampuchea',
                'demo_url' => 'https://khdev.community/projects/deploy-kampuchea',
                'tech_stack' => ['Docker', 'GitHub Actions', 'AWS'],
                'contributors_count' => 6,
                'stars_count' => 131,
                'status' => 'active',
                'looking_for_collaborators' => false,
                'launched_at' => $now->copy()->subWeeks(3)->toDateString(),
            ],
            [
                'user_id' => $users[4]->id,
                'name' => 'KDC Insight Board',
                'slug' => 'kdc-insight-board',
                'tagline' => 'Community analytics for feed quality, event retention, and builder growth.',
                'summary' => 'Pulls activity from posts, projects, and events into an operator dashboard for community leads.',
                'repo_url' => 'https://github.com/example/kdc-insight-board',
                'demo_url' => 'https://khdev.community/projects/kdc-insight-board',
                'tech_stack' => ['Python', 'FastAPI', 'Supabase'],
                'contributors_count' => 4,
                'stars_count' => 96,
                'status' => 'active',
                'looking_for_collaborators' => true,
                'launched_at' => $now->copy()->subWeeks(2)->toDateString(),
            ],
            [
                'user_id' => $users[5]->id,
                'name' => 'HackKit Khmer',
                'slug' => 'hackkit-khmer',
                'tagline' => 'A launch-ready starter kit for student and community hackathons.',
                'summary' => 'Includes team onboarding, judging forms, deployment recipes, and project submission templates.',
                'repo_url' => 'https://github.com/example/hackkit-khmer',
                'demo_url' => 'https://khdev.community/projects/hackkit-khmer',
                'tech_stack' => ['Nuxt', 'Laravel', 'SQLite'],
                'contributors_count' => 9,
                'stars_count' => 121,
                'status' => 'beta',
                'looking_for_collaborators' => true,
                'launched_at' => $now->copy()->subDays(12)->toDateString(),
            ],
            [
                'user_id' => $users[1]->id,
                'name' => 'Builder Profiles UI',
                'slug' => 'builder-profiles-ui',
                'tagline' => 'A reusable profile and portfolio surface for local developer communities.',
                'summary' => 'Focuses on proof-of-work, skill tagging, availability, and collaboration requests in one view.',
                'repo_url' => 'https://github.com/example/builder-profiles-ui',
                'demo_url' => 'https://khdev.community/projects/builder-profiles-ui',
                'tech_stack' => ['Vue', 'Quasar', 'Pinia'],
                'contributors_count' => 3,
                'stars_count' => 88,
                'status' => 'active',
                'looking_for_collaborators' => false,
                'launched_at' => $now->copy()->subDays(8)->toDateString(),
            ],
        ] as $project) {
            Project::updateOrCreate(['slug' => $project['slug']], $project);
        }

        foreach ([
            [
                'host_id' => $users[0]->id,
                'title' => 'Phnom Penh Laravel Night',
                'slug' => 'phnom-penh-laravel-night',
                'summary' => 'Architecture reviews, package demos, and real production lessons.',
                'details' => 'A focused meetup for backend engineers, founders, and students building with Laravel.',
                'format' => 'Hybrid',
                'venue' => 'Factory Phnom Penh',
                'city' => 'Phnom Penh',
                'starts_at' => $now->copy()->addDays(5)->setTime(18, 30),
                'ends_at' => $now->copy()->addDays(5)->setTime(21, 0),
                'capacity' => 120,
                'attendee_count' => 76,
                'interested_count' => 112,
                'registration_url' => 'https://khdev.community/events/phnom-penh-laravel-night',
                'thumbnail_url' => 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?auto=format&fit=crop&w=1200&q=80',
                'organizer_name' => 'Khmer Dev Community',
                'organizer_url' => 'https://khdev.community',
                'is_featured' => true,
                'status' => 'upcoming',
                'published_at' => $now->copy()->subDays(3),
            ],
            [
                'host_id' => $users[2]->id,
                'title' => 'Flutter Ship Room',
                'slug' => 'flutter-ship-room',
                'summary' => 'Async standups, app teardowns, and release coaching.',
                'details' => 'Mobile developers bring current builds and leave with sharper launch plans.',
                'format' => 'Online',
                'venue' => 'Discord Stage',
                'city' => 'Remote',
                'starts_at' => $now->copy()->addDays(9)->setTime(19, 0),
                'ends_at' => $now->copy()->addDays(9)->setTime(20, 30),
                'capacity' => 200,
                'attendee_count' => 118,
                'interested_count' => 166,
                'registration_url' => 'https://khdev.community/events/flutter-ship-room',
                'thumbnail_url' => 'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?auto=format&fit=crop&w=1200&q=80',
                'organizer_name' => 'Mekong Apps',
                'organizer_url' => 'https://khdev.community/u/limvicheka',
                'is_featured' => false,
                'status' => 'upcoming',
                'published_at' => $now->copy()->subDays(4),
            ],
            [
                'host_id' => $users[3]->id,
                'title' => 'Khmer DevOps Office Hours',
                'slug' => 'khmer-devops-office-hours',
                'summary' => 'Bring your pipelines, logs, and deployment blockers.',
                'details' => 'A live troubleshooting session for teams hardening releases and infrastructure.',
                'format' => 'Onsite',
                'venue' => 'KOH PICH Tech Hub',
                'city' => 'Phnom Penh',
                'starts_at' => $now->copy()->addDays(13)->setTime(17, 30),
                'ends_at' => $now->copy()->addDays(13)->setTime(19, 30),
                'capacity' => 80,
                'attendee_count' => 44,
                'interested_count' => 61,
                'registration_url' => 'https://khdev.community/events/khmer-devops-office-hours',
                'thumbnail_url' => 'https://images.unsplash.com/photo-1552664730-d307ca884978?auto=format&fit=crop&w=1200&q=80',
                'organizer_name' => 'Cloud Khmer',
                'organizer_url' => 'https://khdev.community/u/khimnita',
                'is_featured' => false,
                'status' => 'upcoming',
                'published_at' => $now->copy()->subDays(5),
            ],
        ] as $event) {
            CommunityEvent::updateOrCreate(['slug' => $event['slug']], $event);
        }

        foreach ([
            [
                'user_id' => $users[0]->id,
                'company_name' => 'KDC Core',
                'company_logo_url' => 'https://ui-avatars.com/api/?name=KDC+Core&background=0f172a&color=f8fafc',
                'company_website' => 'https://khdev.community',
                'title' => 'Senior Laravel Product Engineer',
                'slug' => 'senior-laravel-product-engineer',
                'summary' => 'Own the API and moderation systems for a Khmer-first developer social platform.',
                'description' => 'You will work across feed, messaging, jobs, moderation, and developer identity systems. Strong Laravel, product thinking, and API design required.',
                'job_type' => 'full_time',
                'work_mode' => 'hybrid',
                'experience_level' => 'senior',
                'location' => 'Phnom Penh',
                'salary_min' => 1800,
                'salary_max' => 2600,
                'salary_currency' => 'USD',
                'tech_stack' => ['Laravel', 'MySQL', 'Redis', 'Vue'],
                'apply_url' => 'https://khdev.community/jobs/senior-laravel-product-engineer/apply',
                'contact_email' => 'jobs@khdev.community',
                'expires_at' => $now->copy()->addDays(21)->toDateString(),
                'status' => 'active',
                'published_at' => $now->copy()->subDays(2),
            ],
            [
                'user_id' => $users[1]->id,
                'company_name' => 'Temple Stack',
                'company_logo_url' => 'https://ui-avatars.com/api/?name=Temple+Stack&background=1d4ed8&color=f8fafc',
                'company_website' => 'https://templestack.dev',
                'title' => 'Frontend Design Systems Contractor',
                'slug' => 'frontend-design-systems-contractor',
                'summary' => 'Help refine builder-facing UI and component quality across web and mobile.',
                'description' => 'Contract role focused on Vue 3, Quasar, systems thinking, accessibility, and readable interfaces for developer-heavy workflows.',
                'job_type' => 'freelance',
                'work_mode' => 'remote',
                'experience_level' => 'mid',
                'location' => 'Remote',
                'salary_min' => 1200,
                'salary_max' => 1800,
                'salary_currency' => 'USD',
                'tech_stack' => ['Vue', 'Quasar', 'Figma', 'Storybook'],
                'apply_url' => 'https://templestack.dev/careers/design-systems-contractor',
                'contact_email' => 'hello@templestack.dev',
                'expires_at' => $now->copy()->addDays(14)->toDateString(),
                'status' => 'active',
                'published_at' => $now->copy()->subDays(1),
            ],
            [
                'user_id' => $users[4]->id,
                'company_name' => 'Insight Mekong',
                'company_logo_url' => 'https://ui-avatars.com/api/?name=Insight+Mekong&background=6d28d9&color=f8fafc',
                'company_website' => 'https://insightmekong.dev',
                'title' => 'Data Engineering Intern',
                'slug' => 'data-engineering-intern',
                'summary' => 'Support community analytics, reporting pipelines, and builder ranking experiments.',
                'description' => 'Great fit for students or juniors who want to learn ETL, dashboards, and product analytics in a real community product.',
                'job_type' => 'internship',
                'work_mode' => 'hybrid',
                'experience_level' => 'intern',
                'location' => 'Phnom Penh',
                'salary_min' => 250,
                'salary_max' => 450,
                'salary_currency' => 'USD',
                'tech_stack' => ['Python', 'SQL', 'Supabase', 'Metabase'],
                'apply_url' => 'https://insightmekong.dev/jobs/data-engineering-intern',
                'contact_email' => 'careers@insightmekong.dev',
                'expires_at' => $now->copy()->addDays(30)->toDateString(),
                'status' => 'active',
                'published_at' => $now->copy()->subHours(20),
            ],
        ] as $job) {
            JobListing::updateOrCreate(['slug' => $job['slug']], $job);
        }

        foreach ([
            [$users[1]->id, $users[0]->id, 'I refined the feed card spacing and the left rail reads much better now.', $now->copy()->subMinutes(18), null],
            [$users[2]->id, $users[0]->id, 'Mobile post composer is ready for the first API pass.', $now->copy()->subHours(2), $now->copy()->subHour()],
            [$users[3]->id, $users[0]->id, 'I can host the first DevOps office hours once registration opens.', $now->copy()->subDay(), $now->copy()->subHours(12)],
        ] as [$senderId, $recipientId, $body, $sentAt, $readAt]) {
            $conversation = Conversation::query()
                ->where('type', 'direct')
                ->whereHas('participants', fn ($query) => $query->where('users.id', $senderId))
                ->whereHas('participants', fn ($query) => $query->where('users.id', $recipientId))
                ->withCount('participants')
                ->get()
                ->first(fn (Conversation $item) => $item->participants_count === 2);

            if (! $conversation) {
                $conversation = Conversation::create([
                    'type' => 'direct',
                    'created_by' => $senderId,
                    'created_at' => $sentAt,
                    'updated_at' => $sentAt,
                ]);

                $conversation->participants()->attach([
                    $senderId => ['joined_at' => $sentAt],
                    $recipientId => ['joined_at' => $sentAt],
                ]);
            }

            $message = Message::updateOrCreate(
                ['conversation_id' => $conversation->id, 'user_id' => $senderId, 'body' => $body],
                ['sent_at' => $sentAt]
            );

            $conversation->forceFill(['updated_at' => $sentAt])->save();

            if ($readAt) {
                $conversation->participants()->updateExistingPivot($recipientId, [
                    'last_read_message_id' => $message->id,
                    'last_read_at' => $readAt,
                ]);
            }
        }

        foreach ([
            [
                'user_id' => $users[0]->id,
                'type' => 'comment',
                'title' => 'New comment on your launch post',
                'body' => 'Chan Ravy left feedback on the v1 launch announcement.',
                'action_url' => '/feed',
                'sent_at' => $now->copy()->subMinutes(20),
                'read_at' => null,
            ],
            [
                'user_id' => $users[0]->id,
                'type' => 'event',
                'title' => 'Event registration crossed 75 members',
                'body' => 'Phnom Penh Laravel Night is gaining traction fast.',
                'action_url' => '/events',
                'sent_at' => $now->copy()->subHours(4),
                'read_at' => $now->copy()->subHours(2),
            ],
            [
                'user_id' => $users[0]->id,
                'type' => 'project',
                'title' => 'A builder asked to join KhmerJobs API',
                'body' => 'A new contributor wants to help with salary normalization.',
                'action_url' => '/projects',
                'sent_at' => $now->copy()->subDay(),
                'read_at' => null,
            ],
        ] as $notification) {
            CommunityNotification::updateOrCreate(
                ['user_id' => $notification['user_id'], 'title' => $notification['title']],
                $notification
            );
        }

        $launchPost = CommunityPost::query()->where('slug', 'launching-khmer-dev-community-v1')->first();
        $feedPost = CommunityPost::query()->where('slug', 'designing-a-familiar-feed')->first();

        foreach ([
            [$launchPost?->id, $users[1]->id, 'The layout balance is much stronger now. The community cards feel easier to scan.'],
            [$launchPost?->id, $users[4]->id, 'Would love to connect the feed trends with analytics so admins can see what topics really move people.'],
            [$feedPost?->id, $users[5]->id, 'Screenshots and contributor asks would make these project cards even more actionable.'],
        ] as [$postId, $userId, $body]) {
            if (! $postId) {
                continue;
            }

            PostComment::updateOrCreate(
                ['post_id' => $postId, 'user_id' => $userId, 'body' => $body],
                ['body' => $body]
            );
        }

        foreach ([
            [$launchPost?->id, $users[1]->id],
            [$launchPost?->id, $users[2]->id],
            [$launchPost?->id, $users[5]->id],
            [$feedPost?->id, $users[0]->id],
            [$feedPost?->id, $users[4]->id],
        ] as [$postId, $userId]) {
            if (! $postId) {
                continue;
            }

            PostLike::updateOrCreate(
                ['post_id' => $postId, 'user_id' => $userId],
                ['created_at' => $now->copy()->subHours(rand(1, 48))]
            );
        }

        CommunityPost::query()->each(function (CommunityPost $post): void {
            $post->forceFill([
                'comments_count' => $post->comments()->count(),
                'likes_count' => $post->likes()->count(),
            ])->save();
        });

        $users[0]->following()->syncWithoutDetaching([$users[1]->id, $users[3]->id]);
        $users[1]->following()->syncWithoutDetaching([$users[0]->id, $users[4]->id]);
        $users[2]->following()->syncWithoutDetaching([$users[0]->id]);

        Bookmark::updateOrCreate([
            'user_id' => $users[0]->id,
            'bookmarkable_type' => CommunityPost::class,
            'bookmarkable_id' => $launchPost?->id,
        ]);

        Bookmark::updateOrCreate([
            'user_id' => $users[0]->id,
            'bookmarkable_type' => Project::class,
            'bookmarkable_id' => Project::query()->where('slug', 'kram-design-system')->value('id'),
        ]);
    }
}
