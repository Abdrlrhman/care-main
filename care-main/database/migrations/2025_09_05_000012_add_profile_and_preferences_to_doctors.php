<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('doctors', function (Blueprint $table) {
            if (!Schema::hasColumn('doctors', 'profile_image')) {
                $table->string('profile_image')->nullable()->after('bio');
            }
            if (!Schema::hasColumn('doctors', 'default_diagnosis_template')) {
                $table->text('default_diagnosis_template')->nullable()->after('profile_image');
            }
            if (!Schema::hasColumn('doctors', 'include_attachments_in_print')) {
                $table->boolean('include_attachments_in_print')->default(true)->after('default_diagnosis_template');
            }
            if (!Schema::hasColumn('doctors', 'notify_email_new_appointment')) {
                $table->boolean('notify_email_new_appointment')->default(true)->after('include_attachments_in_print');
            }
            if (!Schema::hasColumn('doctors', 'notify_sms_new_appointment')) {
                $table->boolean('notify_sms_new_appointment')->default(false)->after('notify_email_new_appointment');
            }
            if (!Schema::hasColumn('doctors', 'notify_email_cancel')) {
                $table->boolean('notify_email_cancel')->default(true)->after('notify_sms_new_appointment');
            }
            if (!Schema::hasColumn('doctors', 'notify_sms_cancel')) {
                $table->boolean('notify_sms_cancel')->default(false)->after('notify_email_cancel');
            }
            if (!Schema::hasColumn('doctors', 'notify_email_overdue_invoice')) {
                $table->boolean('notify_email_overdue_invoice')->default(true)->after('notify_sms_cancel');
            }
            if (!Schema::hasColumn('doctors', 'notify_sms_overdue_invoice')) {
                $table->boolean('notify_sms_overdue_invoice')->default(false)->after('notify_email_overdue_invoice');
            }
        });
    }

    public function down(): void
    {
        Schema::table('doctors', function (Blueprint $table) {
            $cols = [
                'profile_image',
                'default_diagnosis_template',
                'include_attachments_in_print',
                'notify_email_new_appointment',
                'notify_sms_new_appointment',
                'notify_email_cancel',
                'notify_sms_cancel',
                'notify_email_overdue_invoice',
                'notify_sms_overdue_invoice',
            ];
            foreach ($cols as $c) {
                if (Schema::hasColumn('doctors', $c)) {
                    $table->dropColumn($c);
                }
            }
        });
    }
};
