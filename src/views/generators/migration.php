<?php echo '<?php' ?>

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChimpCampaignSetupTables extends Migration {

  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up() {

    Schema::create('mail_chimp_campaigns', function(Blueprint $table) {
        $table->increments('id');

        $table->string('mail_chimp_id')->unique();
        $table->string('view');
        $table->string('status');
        $table->text('extra_data');

        $table->timestamps();
    });

    Schema::create('mail_chimp_campaign_items', function(Blueprint $table) {
        $table->increments('id');

        $table->integer('mail_chimp_campaign_id')->unsigned();
        $table->foreign('mail_chimp_campaign_id')->references('id')->on('mail_chimp_campaigns');

        $table->integer('itemable_id');
        $table->string('itemable_type');

        $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down() {
      Schema::drop('mail_chimp_campaign_items');
      Schema::drop('mail_chimp_campaigns');

  }

}
