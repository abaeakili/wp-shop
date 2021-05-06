<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$row=$this->deliveryTimes;
?>
<div class="wrap">
    <div class="form-wrap">
		<h3><?php echo  $row->id ? _WOP_SHOP_DELIVERY_TIME_EDIT . ' / ' . $row->{Factory::getLang()->get('name')} :  _WOP_SHOP_DELIVERY_TIME_NEW; ?></h3>
        <form method="POST" action="admin.php?page=options&tab=deliverytimes&task=save" id="editdeliverytime">
            <table>
                <?php
                foreach($this->languages as $i=>$v){
                    $name = 'name_'.$v->language;
                ?>
                <tr>
                    <td>
                        <label for="name_<?php echo $v->language; ?>"><?php echo _WOP_SHOP_NAME; ?> (<?php echo $v->name;?>)*</label>
                    </td>
                    <td>
                        <input id="name_<?php echo $v->language; ?>" type="text" size="40" value="<?php echo $row->$name; ?>" name="name_<?php echo $v->language; ?>">
                    </td>
                </tr>
                <?php } ?>
                <tr>
                    <td>
                        <label for="days"><?php echo _WOP_SHOP_DAYS; ?> *</label>
                    </td>
                    <td>
                        <input id="days" type="text" size="40" value="<?php echo $row->days; ?>" name="days">
                    </td>
                </tr>
            </table>

            <p class="submit">
                <input id="submit" class="button button-primary" type="submit" value="<?php echo _WOP_SHOP_ACTION_SAVE; ?>" name="submit">
                <a class="button" id="back" href="admin.php?page=options&tab=deliverytimes"><?php echo _WOP_SHOP_BACK; ?></a>
            </p>
            <input type="hidden" value="<?php echo $row->id; ?>" name="id">
            <?php wp_nonce_field('deliverytimes_edit','name_of_nonce_field'); ?>
        </form>
    </div>
</div>