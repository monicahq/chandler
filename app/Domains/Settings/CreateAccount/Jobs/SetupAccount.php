<?php

namespace App\Domains\Settings\CreateAccount\Jobs;

use App\Domains\Settings\ManageAddressTypes\Services\CreateAddressType;
use App\Domains\Settings\ManageCallReasons\Services\CreateCallReason;
use App\Domains\Settings\ManageCallReasons\Services\CreateCallReasonType;
use App\Domains\Settings\ManageContactInformationTypes\Services\CreateContactInformationType;
use App\Domains\Settings\ManageGenders\Services\CreateGender;
use App\Domains\Settings\ManageGroupTypes\Services\CreateGroupType;
use App\Domains\Settings\ManageGroupTypes\Services\CreateGroupTypeRole;
use App\Domains\Settings\ManageNotificationChannels\Services\CreateUserNotificationChannel;
use App\Domains\Settings\ManagePetCategories\Services\CreatePetCategory;
use App\Domains\Settings\ManagePostTemplates\Services\CreatePostTemplate;
use App\Domains\Settings\ManagePostTemplates\Services\CreatePostTemplateSection;
use App\Domains\Settings\ManagePronouns\Services\CreatePronoun;
use App\Domains\Settings\ManageRelationshipTypes\Services\CreateRelationshipGroupType;
use App\Domains\Settings\ManageTemplates\Services\AssociateModuleToTemplatePage;
use App\Domains\Settings\ManageTemplates\Services\CreateModule;
use App\Domains\Settings\ManageTemplates\Services\CreateTemplate;
use App\Domains\Settings\ManageTemplates\Services\CreateTemplatePage;
use App\Interfaces\ServiceInterface;
use App\Models\Currency;
use App\Models\Emotion;
use App\Models\Module;
use App\Models\RelationshipGroupType;
use App\Models\RelationshipType;
use App\Models\Template;
use App\Models\TemplatePage;
use App\Models\UserNotificationChannel;
use App\Services\QueuableService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SetupAccount extends QueuableService implements ServiceInterface
{
    /**
     * The template instance.
     *
     * @var Template
     */
    protected $template;

    /**
     * Get the validation rules that apply to the service.
     */
    public function rules(): array
    {
        return [
            'account_id' => 'required|uuid|exists:accounts,id',
            'author_id' => 'required|uuid|exists:users,id',
        ];
    }

    /**
     * Get the permissions that apply to the user calling the service.
     */
    public function permissions(): array
    {
        return [
            'author_must_belong_to_account',
        ];
    }

    /**
     * Execute the service.
     */
    public function execute(array $data): void
    {
        $this->validateRules($data);

        $this->populateCurrencies();
        $this->addNotificationChannel();
        $this->addTemplate();
        $this->addTemplatePageContactInformation();
        $this->addTemplatePageFeed();
        $this->addTemplatePageContact();
        $this->addTemplatePageSocial();
        $this->addTemplatePageLifeEvents();
        $this->addTemplatePageInformation();
        $this->addFirstInformation();
    }

    /**
     * Populate currencies in the account.
     */
    private function populateCurrencies(): void
    {
        $currencies = Currency::get();
        foreach ($currencies as $currency) {
            $this->account()->currencies()->attach($currency->id);
        }
    }

    /**
     * Add the first notification channel based on the email address of the user.
     */
    private function addNotificationChannel(): void
    {
        $channel = (new CreateUserNotificationChannel())->execute([
            'account_id' => $this->author->account_id,
            'author_id' => $this->author->id,
            'label' => 'Email address',
            'type' => UserNotificationChannel::TYPE_EMAIL,
            'content' => $this->author->email,
            'verify_email' => false,
            'preferred_time' => '09:00',
        ]);

        $channel->verified_at = Carbon::now();
        $channel->active = true;
        $channel->save();
    }

    /**
     * Add the first template.
     */
    private function addTemplate(): void
    {
        $request = [
            'account_id' => $this->author->account_id,
            'author_id' => $this->author->id,
            'name' => null,
            'name_translation_key' => trans('Default template', locale: 'en'),
        ];

        $this->template = (new CreateTemplate())->execute($request);
    }

    private function addTemplatePageContactInformation(): void
    {
        // the contact information page is automatically created when we
        // create the template
        $templatePageContact = $this->template->pages()
            ->where('type', TemplatePage::TYPE_CONTACT)
            ->first();

        // avatar
        $module = (new CreateModule())->execute([
            'account_id' => $this->author->account_id,
            'author_id' => $this->author->id,
            'name_translation_key' => trans('Avatar', locale: 'en'),
            'type' => Module::TYPE_AVATAR,
            'can_be_deleted' => false,
            'reserved_to_contact_information' => true,
        ]);
        (new AssociateModuleToTemplatePage())->execute([
            'account_id' => $this->author->account_id,
            'author_id' => $this->author->id,
            'template_id' => $this->template->id,
            'template_page_id' => $templatePageContact->id,
            'module_id' => $module->id,
        ]);

        // names
        $module = (new CreateModule())->execute([
            'account_id' => $this->author->account_id,
            'author_id' => $this->author->id,
            'name_translation_key' => trans('Contact name', locale: 'en'),
            'type' => Module::TYPE_CONTACT_NAMES,
            'can_be_deleted' => false,
            'reserved_to_contact_information' => true,
        ]);
        (new AssociateModuleToTemplatePage())->execute([
            'account_id' => $this->author->account_id,
            'author_id' => $this->author->id,
            'template_id' => $this->template->id,
            'template_page_id' => $templatePageContact->id,
            'module_id' => $module->id,
        ]);

        // family summary
        $module = (new CreateModule())->execute([
            'account_id' => $this->author->account_id,
            'author_id' => $this->author->id,
            'name_translation_key' => trans('Family summary', locale: 'en'),
            'type' => Module::TYPE_FAMILY_SUMMARY,
            'can_be_deleted' => false,
            'reserved_to_contact_information' => true,
        ]);
        (new AssociateModuleToTemplatePage())->execute([
            'account_id' => $this->author->account_id,
            'author_id' => $this->author->id,
            'template_id' => $this->template->id,
            'template_page_id' => $templatePageContact->id,
            'module_id' => $module->id,
        ]);

        // important dates
        $module = (new CreateModule())->execute([
            'account_id' => $this->author->account_id,
            'author_id' => $this->author->id,
            'name_translation_key' => trans('Important dates', locale: 'en'),
            'type' => Module::TYPE_IMPORTANT_DATES,
            'can_be_deleted' => false,
            'reserved_to_contact_information' => true,
        ]);
        (new AssociateModuleToTemplatePage())->execute([
            'account_id' => $this->author->account_id,
            'author_id' => $this->author->id,
            'template_id' => $this->template->id,
            'template_page_id' => $templatePageContact->id,
            'module_id' => $module->id,
        ]);

        // gender/pronouns
        $module = (new CreateModule())->execute([
            'account_id' => $this->author->account_id,
            'author_id' => $this->author->id,
            'name_translation_key' => trans('Gender and pronoun', locale: 'en'),
            'type' => Module::TYPE_GENDER_PRONOUN,
            'can_be_deleted' => false,
            'reserved_to_contact_information' => true,
        ]);
        (new AssociateModuleToTemplatePage())->execute([
            'account_id' => $this->author->account_id,
            'author_id' => $this->author->id,
            'template_id' => $this->template->id,
            'template_page_id' => $templatePageContact->id,
            'module_id' => $module->id,
        ]);

        // labels
        $module = (new CreateModule())->execute([
            'account_id' => $this->author->account_id,
            'author_id' => $this->author->id,
            'name_translation_key' => trans('Labels', locale: 'en'),
            'type' => Module::TYPE_LABELS,
            'can_be_deleted' => false,
            'reserved_to_contact_information' => true,
        ]);
        (new AssociateModuleToTemplatePage())->execute([
            'account_id' => $this->author->account_id,
            'author_id' => $this->author->id,
            'template_id' => $this->template->id,
            'template_page_id' => $templatePageContact->id,
            'module_id' => $module->id,
        ]);

        // companies
        $module = (new CreateModule())->execute([
            'account_id' => $this->author->account_id,
            'author_id' => $this->author->id,
            'name_translation_key' => trans('Job information', locale: 'en'),
            'type' => Module::TYPE_COMPANY,
            'can_be_deleted' => false,
            'reserved_to_contact_information' => true,
        ]);
        (new AssociateModuleToTemplatePage())->execute([
            'account_id' => $this->author->account_id,
            'author_id' => $this->author->id,
            'template_id' => $this->template->id,
            'template_page_id' => $templatePageContact->id,
            'module_id' => $module->id,
        ]);

        // religions
        $module = (new CreateModule())->execute([
            'account_id' => $this->author->account_id,
            'author_id' => $this->author->id,
            'name_translation_key' => trans('Religions', locale: 'en'),
            'type' => Module::TYPE_RELIGIONS,
            'can_be_deleted' => false,
            'reserved_to_contact_information' => true,
        ]);
        (new AssociateModuleToTemplatePage())->execute([
            'account_id' => $this->author->account_id,
            'author_id' => $this->author->id,
            'template_id' => $this->template->id,
            'template_page_id' => $templatePageContact->id,
            'module_id' => $module->id,
        ]);
    }

    private function addTemplatePageFeed(): void
    {
        $templatePageFeed = (new CreateTemplatePage())->execute([
            'account_id' => $this->author->account_id,
            'author_id' => $this->author->id,
            'template_id' => $this->template->id,
            'name_translation_key' => trans('Activity feed', locale: 'en'),
            'can_be_deleted' => true,
        ]);
        $module = (new CreateModule())->execute([
            'account_id' => $this->author->account_id,
            'author_id' => $this->author->id,
            'name_translation_key' => trans('Contact feed', locale: 'en'),
            'type' => Module::TYPE_FEED,
            'can_be_deleted' => false,
        ]);
        (new AssociateModuleToTemplatePage())->execute([
            'account_id' => $this->author->account_id,
            'author_id' => $this->author->id,
            'template_id' => $this->template->id,
            'template_page_id' => $templatePageFeed->id,
            'module_id' => $module->id,
        ]);
    }

    private function addTemplatePageContact(): void
    {
        $template = (new CreateTemplatePage())->execute([
            'account_id' => $this->author->account_id,
            'author_id' => $this->author->id,
            'template_id' => $this->template->id,
            'name_translation_key' => trans('Ways to connect', locale: 'en'),
            'can_be_deleted' => true,
        ]);

        // Addresses
        $module = (new CreateModule())->execute([
            'account_id' => $this->author->account_id,
            'author_id' => $this->author->id,
            'name_translation_key' => trans('Addresses', locale: 'en'),
            'type' => Module::TYPE_ADDRESSES,
            'can_be_deleted' => false,
        ]);
        (new AssociateModuleToTemplatePage())->execute([
            'account_id' => $this->author->account_id,
            'author_id' => $this->author->id,
            'template_id' => $this->template->id,
            'template_page_id' => $template->id,
            'module_id' => $module->id,
        ]);

        // Contact information
        $module = (new CreateModule())->execute([
            'account_id' => $this->author->account_id,
            'author_id' => $this->author->id,
            'name_translation_key' => trans('Contact information', locale: 'en'),
            'type' => Module::TYPE_CONTACT_INFORMATION,
            'can_be_deleted' => false,
        ]);
        (new AssociateModuleToTemplatePage())->execute([
            'account_id' => $this->author->account_id,
            'author_id' => $this->author->id,
            'template_id' => $this->template->id,
            'template_page_id' => $template->id,
            'module_id' => $module->id,
        ]);
    }

    private function addTemplatePageSocial(): void
    {
        $templatePageSocial = (new CreateTemplatePage())->execute([
            'account_id' => $this->author->account_id,
            'author_id' => $this->author->id,
            'template_id' => $this->template->id,
            'name_translation_key' => trans('Social', locale: 'en'),
            'can_be_deleted' => true,
        ]);

        // Relationships
        $module = (new CreateModule())->execute([
            'account_id' => $this->author->account_id,
            'author_id' => $this->author->id,
            'name_translation_key' => trans('Relationships', locale: 'en'),
            'type' => Module::TYPE_RELATIONSHIPS,
            'can_be_deleted' => false,
        ]);
        (new AssociateModuleToTemplatePage())->execute([
            'account_id' => $this->author->account_id,
            'author_id' => $this->author->id,
            'template_id' => $this->template->id,
            'template_page_id' => $templatePageSocial->id,
            'module_id' => $module->id,
        ]);

        // Pets
        $module = (new CreateModule())->execute([
            'account_id' => $this->author->account_id,
            'author_id' => $this->author->id,
            'name_translation_key' => trans('Pets', locale: 'en'),
            'type' => Module::TYPE_PETS,
            'can_be_deleted' => false,
        ]);
        (new AssociateModuleToTemplatePage())->execute([
            'account_id' => $this->author->account_id,
            'author_id' => $this->author->id,
            'template_id' => $this->template->id,
            'template_page_id' => $templatePageSocial->id,
            'module_id' => $module->id,
        ]);

        // Groups
        $module = (new CreateModule())->execute([
            'account_id' => $this->author->account_id,
            'author_id' => $this->author->id,
            'name_translation_key' => trans('Groups', locale: 'en'),
            'type' => Module::TYPE_GROUPS,
            'can_be_deleted' => false,
        ]);
        (new AssociateModuleToTemplatePage())->execute([
            'account_id' => $this->author->account_id,
            'author_id' => $this->author->id,
            'template_id' => $this->template->id,
            'template_page_id' => $templatePageSocial->id,
            'module_id' => $module->id,
        ]);
    }

    private function addTemplatePageLifeEvents(): void
    {
        $templatePageSocial = (new CreateTemplatePage())->execute([
            'account_id' => $this->author->account_id,
            'author_id' => $this->author->id,
            'template_id' => $this->template->id,
            'name_translation_key' => trans('Life & goals', locale: 'en'),
            'can_be_deleted' => true,
        ]);

        // life events
        $module = (new CreateModule())->execute([
            'account_id' => $this->author->account_id,
            'author_id' => $this->author->id,
            'name_translation_key' => trans('Life', locale: 'en'),
            'type' => Module::TYPE_LIFE_EVENTS,
            'can_be_deleted' => false,
        ]);
        (new AssociateModuleToTemplatePage())->execute([
            'account_id' => $this->author->account_id,
            'author_id' => $this->author->id,
            'template_id' => $this->template->id,
            'template_page_id' => $templatePageSocial->id,
            'module_id' => $module->id,
        ]);

        // goals
        $module = (new CreateModule())->execute([
            'account_id' => $this->author->account_id,
            'author_id' => $this->author->id,
            'name_translation_key' => trans('Goals', locale: 'en'),
            'type' => Module::TYPE_GOALS,
            'can_be_deleted' => false,
        ]);
        (new AssociateModuleToTemplatePage())->execute([
            'account_id' => $this->author->account_id,
            'author_id' => $this->author->id,
            'template_id' => $this->template->id,
            'template_page_id' => $templatePageSocial->id,
            'module_id' => $module->id,
        ]);
    }

    private function addTemplatePageInformation(): void
    {
        $templatePageInformation = (new CreateTemplatePage())->execute([
            'account_id' => $this->author->account_id,
            'author_id' => $this->author->id,
            'template_id' => $this->template->id,
            'name_translation_key' => trans('Information', locale: 'en'),
            'can_be_deleted' => true,
        ]);

        // Documents
        $module = (new CreateModule())->execute([
            'account_id' => $this->author->account_id,
            'author_id' => $this->author->id,
            'name_translation_key' => trans('Documents', locale: 'en'),
            'type' => Module::TYPE_DOCUMENTS,
            'can_be_deleted' => false,
        ]);
        (new AssociateModuleToTemplatePage())->execute([
            'account_id' => $this->author->account_id,
            'author_id' => $this->author->id,
            'template_id' => $this->template->id,
            'template_page_id' => $templatePageInformation->id,
            'module_id' => $module->id,
        ]);

        // Documents
        $module = (new CreateModule())->execute([
            'account_id' => $this->author->account_id,
            'author_id' => $this->author->id,
            'name_translation_key' => trans('Photos', locale: 'en'),
            'type' => Module::TYPE_PHOTOS,
            'can_be_deleted' => false,
        ]);
        (new AssociateModuleToTemplatePage())->execute([
            'account_id' => $this->author->account_id,
            'author_id' => $this->author->id,
            'template_id' => $this->template->id,
            'template_page_id' => $templatePageInformation->id,
            'module_id' => $module->id,
        ]);

        // Notes
        $module = (new CreateModule())->execute([
            'account_id' => $this->author->account_id,
            'author_id' => $this->author->id,
            'name_translation_key' => trans('Notes', locale: 'en'),
            'type' => Module::TYPE_NOTES,
            'can_be_deleted' => false,
            'pagination' => 3,
        ]);
        (new AssociateModuleToTemplatePage())->execute([
            'account_id' => $this->author->account_id,
            'author_id' => $this->author->id,
            'template_id' => $this->template->id,
            'template_page_id' => $templatePageInformation->id,
            'module_id' => $module->id,
        ]);

        // Reminders
        $module = (new CreateModule())->execute([
            'account_id' => $this->author->account_id,
            'author_id' => $this->author->id,
            'name_translation_key' => trans('Reminders', locale: 'en'),
            'type' => Module::TYPE_REMINDERS,
            'can_be_deleted' => false,
        ]);
        (new AssociateModuleToTemplatePage())->execute([
            'account_id' => $this->author->account_id,
            'author_id' => $this->author->id,
            'template_id' => $this->template->id,
            'template_page_id' => $templatePageInformation->id,
            'module_id' => $module->id,
        ]);

        // Loans
        $module = (new CreateModule())->execute([
            'account_id' => $this->author->account_id,
            'author_id' => $this->author->id,
            'name_translation_key' => trans('Loans', locale: 'en'),
            'type' => Module::TYPE_LOANS,
            'can_be_deleted' => false,
        ]);
        (new AssociateModuleToTemplatePage())->execute([
            'account_id' => $this->author->account_id,
            'author_id' => $this->author->id,
            'template_id' => $this->template->id,
            'template_page_id' => $templatePageInformation->id,
            'module_id' => $module->id,
        ]);

        // Tasks
        $module = (new CreateModule())->execute([
            'account_id' => $this->author->account_id,
            'author_id' => $this->author->id,
            'name_translation_key' => trans('Tasks', locale: 'en'),
            'type' => Module::TYPE_TASKS,
            'can_be_deleted' => false,
        ]);
        (new AssociateModuleToTemplatePage())->execute([
            'account_id' => $this->author->account_id,
            'author_id' => $this->author->id,
            'template_id' => $this->template->id,
            'template_page_id' => $templatePageInformation->id,
            'module_id' => $module->id,
        ]);

        // Calls
        $module = (new CreateModule())->execute([
            'account_id' => $this->author->account_id,
            'author_id' => $this->author->id,
            'name_translation_key' => trans('Calls', locale: 'en'),
            'type' => Module::TYPE_CALLS,
            'can_be_deleted' => false,
        ]);
        (new AssociateModuleToTemplatePage())->execute([
            'account_id' => $this->author->account_id,
            'author_id' => $this->author->id,
            'template_id' => $this->template->id,
            'template_page_id' => $templatePageInformation->id,
            'module_id' => $module->id,
        ]);

        // Posts
        $module = (new CreateModule())->execute([
            'account_id' => $this->author->account_id,
            'author_id' => $this->author->id,
            'name_translation_key' => trans('Posts', locale: 'en'),
            'type' => Module::TYPE_POSTS,
            'can_be_deleted' => false,
        ]);
        (new AssociateModuleToTemplatePage())->execute([
            'account_id' => $this->author->account_id,
            'author_id' => $this->author->id,
            'template_id' => $this->template->id,
            'template_page_id' => $templatePageInformation->id,
            'module_id' => $module->id,
        ]);
    }

    private function addFirstInformation(): void
    {
        $this->addGenders();
        $this->addPronouns();
        $this->addGroupTypes();
        $this->addRelationshipTypes();
        $this->addAddressTypes();
        $this->addCallReasonTypes();
        $this->addContactInformation();
        $this->addPetCategories();
        $this->addEmotions();
        $this->addGiftOccasions();
        $this->addGiftStates();
        $this->addPostTemplates();
        $this->addReligions();
    }

    /**
     * Add the default genders in the account.
     */
    private function addGenders(): void
    {
        $types = collect([
            trans('Male', locale: 'en'),
            trans('Female', locale: 'en'),
            trans('Other', locale: 'en'),
        ]);

        foreach ($types as $type) {
            $request = [
                'account_id' => $this->author->account_id,
                'author_id' => $this->author->id,
                'name_translation_key' => $type,
            ];

            (new CreateGender())->execute($request);
        }
    }

    /**
     * Add the default pronouns in the account.
     */
    private function addPronouns(): void
    {
        $pronouns = collect([
            trans('he/him', locale: 'en'),
            trans('she/her', locale: 'en'),
            trans('they/them', locale: 'en'),
            trans('per/per', locale: 'en'),
            trans('ve/ver', locale: 'en'),
            trans('xe/xem', locale: 'en'),
            trans('ze/hir', locale: 'en'),
        ]);

        foreach ($pronouns as $pronoun) {
            $request = [
                'account_id' => $this->author->account_id,
                'author_id' => $this->author->id,
                'name_translation_key' => $pronoun,
            ];

            (new CreatePronoun())->execute($request);
        }
    }

    /**
     * Add the default group types in the account.
     */
    private function addGroupTypes(): void
    {
        $groupType = (new CreateGroupType())->execute([
            'account_id' => $this->author->account_id,
            'author_id' => $this->author->id,
            'label_translation_key' => trans('Family', locale: 'en'),
        ]);
        (new CreateGroupTypeRole())->execute([
            'account_id' => $this->author->account_id,
            'author_id' => $this->author->id,
            'group_type_id' => $groupType->id,
            'label_translation_key' => trans('Parent', locale: 'en'),
        ]);
        (new CreateGroupTypeRole())->execute([
            'account_id' => $this->author->account_id,
            'author_id' => $this->author->id,
            'group_type_id' => $groupType->id,
            'label_translation_key' => trans('Child', locale: 'en'),
        ]);

        $groupType = (new CreateGroupType())->execute([
            'account_id' => $this->author->account_id,
            'author_id' => $this->author->id,
            'label_translation_key' => trans('Couple', locale: 'en'),
        ]);
        (new CreateGroupTypeRole())->execute([
            'account_id' => $this->author->account_id,
            'author_id' => $this->author->id,
            'group_type_id' => $groupType->id,
            'label_translation_key' => trans('Partner', locale: 'en'),
        ]);

        $groupType = (new CreateGroupType())->execute([
            'account_id' => $this->author->account_id,
            'author_id' => $this->author->id,
            'label_translation_key' => trans('Club', locale: 'en'),
        ]);

        $groupType = (new CreateGroupType())->execute([
            'account_id' => $this->author->account_id,
            'author_id' => $this->author->id,
            'label_translation_key' => trans('Association', locale: 'en'),
        ]);

        $groupType = (new CreateGroupType())->execute([
            'account_id' => $this->author->account_id,
            'author_id' => $this->author->id,
            'label_translation_key' => trans('Roomates', locale: 'en'),
        ]);
    }

    private function addRelationshipTypes(): void
    {
        // Love type
        $group = (new CreateRelationshipGroupType())->execute([
            'account_id' => $this->author->account_id,
            'author_id' => $this->author->id,
            'name_translation_key' => trans('Love', locale: 'en'),
            'can_be_deleted' => false,
            'type' => RelationshipGroupType::TYPE_LOVE,
        ]);

        DB::table('relationship_types')->insert([
            [
                'name_translation_key' => trans('significant other', locale: 'en'),
                'name_reverse_relationship_translation_key' => trans('significant other', locale: 'en'),
                'relationship_group_type_id' => $group->id,
                'can_be_deleted' => false,
                'type' => RelationshipType::TYPE_LOVE,
            ],
            [
                'name_translation_key' => trans('spouse', locale: 'en'),
                'name_reverse_relationship_translation_key' => trans('spouse', locale: 'en'),
                'relationship_group_type_id' => $group->id,
                'can_be_deleted' => false,
                'type' => RelationshipType::TYPE_LOVE,
            ],
            [
                'name_translation_key' => trans('date', locale: 'en'),
                'name_reverse_relationship_translation_key' => trans('date', locale: 'en'),
                'relationship_group_type_id' => $group->id,
                'can_be_deleted' => true,
                'type' => null,
            ],
            [
                'name_translation_key' => trans('lover', locale: 'en'),
                'name_reverse_relationship_translation_key' => trans('lover', locale: 'en'),
                'relationship_group_type_id' => $group->id,
                'can_be_deleted' => true,
                'type' => null,
            ],
            [
                'name_translation_key' => trans('in love with', locale: 'en'),
                'name_reverse_relationship_translation_key' => trans('loved by', locale: 'en'),
                'relationship_group_type_id' => $group->id,
                'can_be_deleted' => true,
                'type' => null,
            ],
            [
                'name_translation_key' => trans('ex-boyfriend', locale: 'en'),
                'name_reverse_relationship_translation_key' => trans('ex-boyfriend', locale: 'en'),
                'relationship_group_type_id' => $group->id,
                'can_be_deleted' => true,
                'type' => null,
            ],
        ]);

        // Family type
        $group = (new CreateRelationshipGroupType())->execute([
            'account_id' => $this->author->account_id,
            'author_id' => $this->author->id,
            'name_translation_key' => trans('Family', locale: 'en'),
            'can_be_deleted' => false,
            'type' => RelationshipGroupType::TYPE_FAMILY,
        ]);

        DB::table('relationship_types')->insert([
            [
                'name_translation_key' => trans('parent', locale: 'en'),
                'name_reverse_relationship_translation_key' => trans('child', locale: 'en'),
                'relationship_group_type_id' => $group->id,
                'can_be_deleted' => false,
                'type' => RelationshipType::TYPE_CHILD,
            ],
            [
                'name_translation_key' => trans('brother/sister', locale: 'en'),
                'name_reverse_relationship_translation_key' => trans('brother/sister', locale: 'en'),
                'relationship_group_type_id' => $group->id,
                'can_be_deleted' => true,
                'type' => null,
            ],
            [
                'name_translation_key' => trans('grand parent', locale: 'en'),
                'name_reverse_relationship_translation_key' => trans('grand child', locale: 'en'),
                'relationship_group_type_id' => $group->id,
                'can_be_deleted' => true,
                'type' => null,
            ],
            [
                'name_translation_key' => trans('uncle/aunt', locale: 'en'),
                'name_reverse_relationship_translation_key' => trans('nephew/niece', locale: 'en'),
                'relationship_group_type_id' => $group->id,
                'can_be_deleted' => true,
                'type' => null,
            ],
            [
                'name_translation_key' => trans('cousin', locale: 'en'),
                'name_reverse_relationship_translation_key' => trans('cousin', locale: 'en'),
                'relationship_group_type_id' => $group->id,
                'can_be_deleted' => true,
                'type' => null,
            ],
            [
                'name_translation_key' => trans('godparent', locale: 'en'),
                'name_reverse_relationship_translation_key' => trans('godchild', locale: 'en'),
                'relationship_group_type_id' => $group->id,
                'can_be_deleted' => true,
                'type' => null,
            ],
        ]);

        // Friend
        $group = (new CreateRelationshipGroupType())->execute([
            'account_id' => $this->author->account_id,
            'author_id' => $this->author->id,
            'name_translation_key' => trans('Friend', locale: 'en'),
            'can_be_deleted' => true,
        ]);

        DB::table('relationship_types')->insert([
            [
                'name_translation_key' => trans('friend', locale: 'en'),
                'name_reverse_relationship_translation_key' => trans('friend', locale: 'en'),
                'relationship_group_type_id' => $group->id,
                'can_be_deleted' => true,
                'type' => null,
            ],
            [
                'name_translation_key' => trans('best friend', locale: 'en'),
                'name_reverse_relationship_translation_key' => trans('best friend', locale: 'en'),
                'relationship_group_type_id' => $group->id,
                'can_be_deleted' => true,
                'type' => null,
            ],
        ]);

        // Work
        $group = (new CreateRelationshipGroupType())->execute([
            'account_id' => $this->author->account_id,
            'author_id' => $this->author->id,
            'name_translation_key' => trans('Work', locale: 'en'),
            'can_be_deleted' => true,
        ]);

        DB::table('relationship_types')->insert([
            [
                'name_translation_key' => trans('colleague', locale: 'en'),
                'name_reverse_relationship_translation_key' => trans('colleague', locale: 'en'),
                'relationship_group_type_id' => $group->id,
                'can_be_deleted' => true,
                'type' => null,
            ],
            [
                'name_translation_key' => trans('subordinate', locale: 'en'),
                'name_reverse_relationship_translation_key' => trans('boss', locale: 'en'),
                'relationship_group_type_id' => $group->id,
                'can_be_deleted' => true,
                'type' => null,
            ],
            [
                'name_translation_key' => trans('mentor', locale: 'en'),
                'name_reverse_relationship_translation_key' => trans('protege', locale: 'en'),
                'relationship_group_type_id' => $group->id,
                'can_be_deleted' => true,
                'type' => null,
            ],
        ]);
    }

    private function addAddressTypes(): void
    {
        $addresses = collect([
            trans('🏡 Home', locale: 'en'),
            trans('🏠 Secondary residence', locale: 'en'),
            trans('🏢 Work', locale: 'en'),
            trans('🌳 Chalet', locale: 'en'),
        ]);

        foreach ($addresses as $address) {
            (new CreateAddressType())->execute([
                'account_id' => $this->author->account_id,
                'author_id' => $this->author->id,
                'name_translation_key' => $address,
            ]);
        }
    }

    private function addCallReasonTypes(): void
    {
        $type = (new CreateCallReasonType())->execute([
            'account_id' => $this->author->account_id,
            'author_id' => $this->author->id,
            'label_translation_key' => trans('Personal', locale: 'en'),
        ]);
        (new CreateCallReason())->execute([
            'account_id' => $this->author->account_id,
            'author_id' => $this->author->id,
            'call_reason_type_id' => $type->id,
            'label_translation_key' => trans('For advice', locale: 'en'),
        ]);
        (new CreateCallReason())->execute([
            'account_id' => $this->author->account_id,
            'author_id' => $this->author->id,
            'call_reason_type_id' => $type->id,
            'label_translation_key' => trans('Just to say hello', locale: 'en'),
        ]);
        (new CreateCallReason())->execute([
            'account_id' => $this->author->account_id,
            'author_id' => $this->author->id,
            'call_reason_type_id' => $type->id,
            'label_translation_key' => trans('To see if they need anything', locale: 'en'),
        ]);
        (new CreateCallReason())->execute([
            'account_id' => $this->author->account_id,
            'author_id' => $this->author->id,
            'call_reason_type_id' => $type->id,
            'label_translation_key' => trans('Out of respect and appreciation', locale: 'en'),
        ]);
        (new CreateCallReason())->execute([
            'account_id' => $this->author->account_id,
            'author_id' => $this->author->id,
            'call_reason_type_id' => $type->id,
            'label_translation_key' => trans('To hear their story', locale: 'en'),
        ]);
        (new CreateCallReason())->execute([
            'account_id' => $this->author->account_id,
            'author_id' => $this->author->id,
            'call_reason_type_id' => $type->id,
            'label_translation_key' => trans('', locale: 'en'),
        ]);

        // business
        $type = (new CreateCallReasonType())->execute([
            'account_id' => $this->author->account_id,
            'author_id' => $this->author->id,
            'label_translation_key' => trans('Business', locale: 'en'),
        ]);
        (new CreateCallReason())->execute([
            'account_id' => $this->author->account_id,
            'author_id' => $this->author->id,
            'call_reason_type_id' => $type->id,
            'label_translation_key' => trans('Discuss recent purchases', locale: 'en'),
        ]);
        (new CreateCallReason())->execute([
            'account_id' => $this->author->account_id,
            'author_id' => $this->author->id,
            'call_reason_type_id' => $type->id,
            'label_translation_key' => trans('Discuss partnership', locale: 'en'),
        ]);
    }

    private function addContactInformation(): void
    {
        $information = (new CreateContactInformationType())->execute([
            'account_id' => $this->author->account_id,
            'author_id' => $this->author->id,
            'name_translation_key' => trans('Email', locale: 'en'),
            'protocol' => 'mailto:',
        ]);
        $information->can_be_deleted = false;
        $information->type = 'email';
        $information->save();

        $information = (new CreateContactInformationType())->execute([
            'account_id' => $this->author->account_id,
            'author_id' => $this->author->id,
            'name_translation_key' => trans('Phone', locale: 'en'),
            'protocol' => 'tel:',
        ]);
        $information->can_be_deleted = false;
        $information->type = 'phone';
        $information->save();

        (new CreateContactInformationType())->execute([
            'account_id' => $this->author->account_id,
            'author_id' => $this->author->id,
            'name' => 'Facebook',
        ]);
        (new CreateContactInformationType())->execute([
            'account_id' => $this->author->account_id,
            'author_id' => $this->author->id,
            'name' => 'Twitter',
        ]);
        (new CreateContactInformationType())->execute([
            'account_id' => $this->author->account_id,
            'author_id' => $this->author->id,
            'name' => 'Whatsapp',
        ]);
        (new CreateContactInformationType())->execute([
            'account_id' => $this->author->account_id,
            'author_id' => $this->author->id,
            'name' => 'Telegram',
        ]);
        (new CreateContactInformationType())->execute([
            'account_id' => $this->author->account_id,
            'author_id' => $this->author->id,
            'name' => 'Hangouts',
        ]);
        (new CreateContactInformationType())->execute([
            'account_id' => $this->author->account_id,
            'author_id' => $this->author->id,
            'name' => 'Linkedin',
        ]);
        (new CreateContactInformationType())->execute([
            'account_id' => $this->author->account_id,
            'author_id' => $this->author->id,
            'name' => 'Instagram',
        ]);
    }

    private function addPetCategories(): void
    {
        $categories = collect([
            trans('Dog', locale: 'en'),
            trans('Cat', locale: 'en'),
            trans('Bird', locale: 'en'),
            trans('Fish', locale: 'en'),
            trans('Small animal', locale: 'en'),
            trans('Hamster', locale: 'en'),
            trans('Horse', locale: 'en'),
            trans('Rabbit', locale: 'en'),
            trans('Rat', locale: 'en'),
            trans('Reptile', locale: 'en'),
        ]);

        foreach ($categories as $category) {
            (new CreatePetCategory())->execute([
                'account_id' => $this->author->account_id,
                'author_id' => $this->author->id,
                'name_translation_key' => $category,
            ]);
        }
    }

    private function addEmotions(): void
    {
        DB::table('emotions')->insert([
            [
                'account_id' => $this->author->account_id,
                'name_translation_key' => trans('😡 Negative', locale: 'en'),
                'type' => Emotion::TYPE_NEGATIVE,
            ],
            [
                'account_id' => $this->author->account_id,
                'name_translation_key' => trans('😶‍🌫️ Neutral', locale: 'en'),
                'type' => Emotion::TYPE_NEUTRAL,
            ],
            [
                'account_id' => $this->author->account_id,
                'name_translation_key' => trans('😁 Positive', locale: 'en'),
                'type' => Emotion::TYPE_POSITIVE,
            ],
        ]);
    }

    private function addGiftOccasions(): void
    {
        DB::table('gift_occasions')->insert([
            [
                'account_id' => $this->author->account_id,
                'label_translation_key' => trans('Birthday', locale: 'en'),
                'position' => 1,
            ],
            [
                'account_id' => $this->author->account_id,
                'label_translation_key' => trans('Anniversary', locale: 'en'),
                'position' => 2,
            ],
            [
                'account_id' => $this->author->account_id,
                'label_translation_key' => trans('Christmas', locale: 'en'),
                'position' => 3,
            ],
            [
                'account_id' => $this->author->account_id,
                'label_translation_key' => trans('Just because', locale: 'en'),
                'position' => 4,
            ],
            [
                'account_id' => $this->author->account_id,
                'label_translation_key' => trans('Wedding', locale: 'en'),
                'position' => 5,
            ],
        ]);
    }

    private function addGiftStates(): void
    {
        DB::table('gift_states')->insert([
            [
                'account_id' => $this->author->account_id,
                'label_translation_key' => trans('Idea', locale: 'en'),
                'position' => 1,
            ],
            [
                'account_id' => $this->author->account_id,
                'label_translation_key' => trans('Searched', locale: 'en'),
                'position' => 2,
            ],
            [
                'account_id' => $this->author->account_id,
                'label_translation_key' => trans('Found', locale: 'en'),
                'position' => 3,
            ],
            [
                'account_id' => $this->author->account_id,
                'label_translation_key' => trans('Bought', locale: 'en'),
                'position' => 4,
            ],
            [
                'account_id' => $this->author->account_id,
                'label_translation_key' => trans('Offered', locale: 'en'),
                'position' => 5,
            ],
        ]);
    }

    private function addPostTemplates(): void
    {
        // default template
        $postTemplate = (new CreatePostTemplate())->execute([
            'account_id' => $this->author->account_id,
            'author_id' => $this->author->id,
            'label_translation_key' => trans('Regular post', locale: 'en'),
            'can_be_deleted' => false,
        ]);

        (new CreatePostTemplateSection())->execute([
            'account_id' => $this->author->account_id,
            'author_id' => $this->author->id,
            'post_template_id' => $postTemplate->id,
            'label_translation_key' => trans('Content', locale: 'en'),
            'can_be_deleted' => false,
        ]);

        // inspirational template
        $postTemplate = (new CreatePostTemplate())->execute([
            'account_id' => $this->author->account_id,
            'author_id' => $this->author->id,
            'label_translation_key' => trans('Inspirational post', locale: 'en'),
            'can_be_deleted' => true,
        ]);

        (new CreatePostTemplateSection())->execute([
            'account_id' => $this->author->account_id,
            'author_id' => $this->author->id,
            'post_template_id' => $postTemplate->id,
            'label_translation_key' => trans('I am grateful for', locale: 'en'),
            'can_be_deleted' => true,
        ]);
        (new CreatePostTemplateSection())->execute([
            'account_id' => $this->author->account_id,
            'author_id' => $this->author->id,
            'post_template_id' => $postTemplate->id,
            'label_translation_key' => trans('Daily affirmation', locale: 'en'),
            'can_be_deleted' => true,
        ]);
        (new CreatePostTemplateSection())->execute([
            'account_id' => $this->author->account_id,
            'author_id' => $this->author->id,
            'post_template_id' => $postTemplate->id,
            'label_translation_key' => trans('How could I have done this day better?', locale: 'en'),
            'can_be_deleted' => true,
        ]);
        (new CreatePostTemplateSection())->execute([
            'account_id' => $this->author->account_id,
            'author_id' => $this->author->id,
            'post_template_id' => $postTemplate->id,
            'label_translation_key' => trans('What would make today great?', locale: 'en'),
            'can_be_deleted' => true,
        ]);
        (new CreatePostTemplateSection())->execute([
            'account_id' => $this->author->account_id,
            'author_id' => $this->author->id,
            'post_template_id' => $postTemplate->id,
            'label_translation_key' => trans('Three things that happened today', locale: 'en'),
            'can_be_deleted' => true,
        ]);
    }

    private function addReligions(): void
    {
        DB::table('religions')->insert([
            [
                'account_id' => $this->author->account_id,
                'translation_key' => trans('Christian', locale: 'en'),
                'position' => 1,
            ],
            [
                'account_id' => $this->author->account_id,
                'translation_key' => trans('Muslim', locale: 'en'),
                'position' => 2,
            ],
            [
                'account_id' => $this->author->account_id,
                'translation_key' => trans('Hinduist', locale: 'en'),
                'position' => 3,
            ],
            [
                'account_id' => $this->author->account_id,
                'translation_key' => trans('Buddhist', locale: 'en'),
                'position' => 4,
            ],
            [
                'account_id' => $this->author->account_id,
                'translation_key' => trans('Shintoist', locale: 'en'),
                'position' => 5,
            ],
            [
                'account_id' => $this->author->account_id,
                'translation_key' => trans('Taoist', locale: 'en'),
                'position' => 6,
            ],
            [
                'account_id' => $this->author->account_id,
                'translation_key' => trans('Sikh', locale: 'en'),
                'position' => 7,
            ],
            [
                'account_id' => $this->author->account_id,
                'translation_key' => trans('Jew', locale: 'en'),
                'position' => 8,
            ],
            [
                'account_id' => $this->author->account_id,
                'translation_key' => trans('Atheist', locale: 'en'),
                'position' => 9,
            ],
        ]);
    }
}
