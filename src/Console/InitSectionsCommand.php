<?php

namespace LaravelCms\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;
use LaravelCms\LaravelCmsServiceProvider;
use LaravelCms\Models\Cms\Section;
use LaravelCms\Models\Cms\SectionGroup;
use LaravelCms\Models\Cms\User;

class InitSectionsCommand extends Command
{
    protected $signature = 'cms:init-sections';

    protected $description = 'Add sections records in DB';

    public function handle()
    {
        $this->initDbRecords();
    }

    protected function initDbRecords(): self
    {
        // Groups
        if (!SectionGroup::where('name', 'System')->first()) {
            $group = SectionGroup::create([
                'name' => 'System',
                'sort_order' => 1,
                'is_published' => true,
            ]);
        }

        // Sections
        if (!Section::where('folder', 'section-groups')->first()) {
            $groups = Section::create([
                'name' => 'Groups',
                'folder' => 'section-groups',
                'cms_section_group_id' => $group->getKey(),
                'is_published' => true,
            ]);
        }

        if (!Section::where('folder', 'sections')->first()) {
            $sections = Section::firstOrCreate([
                'name' => 'Sections',
                'folder' => 'sections',
                'cms_section_group_id' => $group->getKey(),
                'is_published' => true,
            ]);
        }

        if (!Section::where('folder', 'users')->first()) {
            $users = Section::firstOrCreate([
                'name' => 'Users',
                'folder' => 'users',
                'cms_section_group_id' => $group->getKey(),
                'is_published' => true,
            ]);
        }

        if (!Section::where('folder', 'settings')->first()) {
            $settings = Section::firstOrCreate([
                'name' => 'Settings',
                'folder' => 'settings',
                'cms_section_group_id' => $group->getKey(),
                'is_published' => true,
            ]);
        }

        $this->info('Sections records were added');

        return $this;
    }
}
