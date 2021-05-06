<?php
    if (!defined('ABSPATH')) {
        exit; // Exit if accessed directly
    }
    $config = Factory::getConfig();
    $row = $this->category;
    $lists = $this->lists;
?>
<form method="POST" action="admin.php?page=categories&task=save" enctype="multipart/form-data">
<div class="wrap">
	  <h3><?php echo  $row->category_id ? _WOP_SHOP_EDIT_CATEGORY . ' / ' . $row->{Factory::getLang()->get('name')} :  _WOP_SHOP_NEW_CATEGORY; ?></h3>
    <div class="tabs">
        <?php print $this->tmp_html_start?>
        <ul class="tab-links">
            <li class="active"><a href="#tabInfo"><?php echo _WOP_SHOP_MAIN_PARAMETERS; ?></a></li>
            <li><a href="#tabDescription"><?php echo _WOP_SHOP_DESCRIPTION; ?></a></li>
            <li><a href="#tabImages"><?php echo _WOP_SHOP_IMAGE; ?></a></li>
        </ul>
        <div class="tab-content">
            <div id="tabInfo" class="tab active">
                <div class="col100">
                    <table class="admintable" >
                        <tr>
                          <td class="key">
                            <?php echo _WOP_SHOP_PUBLISH;?>
                          </td>
                          <td>
                            <input type = "checkbox" name = "category_publish" value = "1" <?php if ($row->category_publish) echo 'checked = "checked"'?> />
                          </td>
                        </tr>
                        <tr>
                        <td class="key">
                          <?php echo _WOP_SHOP_ACCESS;?>*
                        </td>
                        <td>
                          <?php print $this->lists['access'];?>
                        </td>
                      </tr>
                        <tr>
                          <td  class="key">
                            <?php echo _WOP_SHOP_ORDERING_CATEGORY;?>
                          </td>
                          <td id = "ordering">
                            <?php echo $lists['onelevel']?>
                          </td>
                        </tr>
                        <?php if ($config->use_different_templates_cat_prod) { ?>
                        <tr>
                          <td  class="key">
                            <?php echo _WOP_SHOP_TEMPLATE_CATEGORY;?>
                          </td>
                          <td>
                            <?php echo $lists['templates']?>
                          </td>
                        </tr>
                        <?php } ?>

                        <tr>
                          <td  class="key">
                            <?php echo _WOP_SHOP_COUNT_PRODUCTS_PAGE;?>*
                          </td>
                          <td>
                            <input type = "text" class = "inputbox" id = "products_page" name = "products_page" value = "<?php echo $count_product_page = ($row->category_id) ? ($row->products_page) : ($config->count_products_to_page);?>" />
                          </td>
                        </tr>
                        <tr>
                          <td  class="key">
                            <?php echo _WOP_SHOP_COUNT_PRODUCTS_ROW;?>*
                          </td>
                          <td>
                            <input type = "text" class = "inputbox" id = "products_row" name = "products_row" value = "<?php echo $count_product_row = ($row->category_id) ? ($row->products_row) : ($config->count_products_to_row);?>" />
                          </td>
                        </tr>
                        <tr>
                          <td  class="key">
                            <?php echo _WOP_SHOP_PARENT_CATEGORY;?>*
                          </td>
                          <td>
                            <?php echo $lists['treecategories'];?>
                          </td>
                        </tr>
                        <?php $pkey="etemplatevar";if ($this->$pkey){print $this->$pkey;}?>
                      </table>
                    <div class="clr"></div>
                </div>
            </div>
            <div id="tabDescription" class="tab">
                <?php if ($this->multilang) : ?>
                    <div class="tabs">
                        <ul class="tab-links">
                            <?php foreach ($this->languages as $index => $lang) : ?>
                                <li><a href="#tab<?php echo $index; ?>"><?php echo _WOP_SHOP_DESCRIPTION; ?> <?php echo $lang->name; ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                    <div class="tab-content">
                <?php endif; ?>
                        <?php
                        foreach($this->languages as $index=>$lang){
                            $name = "name_".$lang->language;
                            $alias = "alias_".$lang->language;
                            $description = "description_".$lang->language;
                            $short_description = "short_description_".$lang->language;
                            $meta_title = "meta_title_".$lang->language;
                            $meta_keyword = "meta_keyword_".$lang->language;
                            $meta_description = "meta_description_".$lang->language;
                        ?>
                            <?php if ($this->multilang) : ?>
                                <div class="tab" id="tab<?php echo $index; ?>">
                            <?php endif; ?>
                                <table class="admintable">
                                    <tr>
                                      <td class="key">
                                        <?php echo _WOP_SHOP_TITLE;?>*
                                      </td>
                                      <td>
                                        <input type = "text" class = "inputbox" size = "60%" name = "<?php print $name?>" value = "<?php print $row->$name; ?>" />
                                      </td>
                                    </tr>
                                    <tr>
                                      <td class="key">
                                        <?php echo _WOP_SHOP_ALIAS;?>
                                      </td>
                                      <td>
                                        <input type = "text" class = "inputbox" size = "60%" name = "<?php print $alias?>" value = "<?php print $row->$alias?>" />
                                      </td>
                                    </tr>
                                    <tr>
                                      <td  class="key">
                                        <?php echo _WOP_SHOP_SHORT_DESCRIPTION;?>
                                      </td>
                                      <td>
                                          <textarea class="inputbox" name = "<?php print $short_description;?>" cols = "55" rows="5"><?php echo $row->$short_description ?></textarea>
                                      </td>
                                    </tr>
                                    <tr>
                                      <td  class="key">
                                        <?php echo _WOP_SHOP_DESCRIPTION;?>
                                      </td>
                                      <td>
                                        <?php
                                            $args = array('media_buttons' => 1,'textarea_name' => "description".$lang->id,'textarea_rows' => 20,'tabindex' => null,'tinymce' => 1,);
                                            wp_editor( $row->$description, "description".$lang->id, $args );             
                                        ?>
                                      </td>
                                    </tr>
                                    <tr>
                                      <td  class="key">
                                        <?php echo _WOP_SHOP_META_TITLE;?>
                                      </td>
                                      <td>
                                        <input type = "text" class = "inputbox" size = "60%" name = "<?php print $meta_title?>" value = "<?php print $row->$meta_title?>" />
                                      </td>
                                    </tr>
                                    <tr>
                                      <td  class="key">
                                        <?php echo _WOP_SHOP_META_DESCRIPTION;?>
                                      </td>
                                      <td>
                                        <input type = "text" class = "inputbox" size = "60%" name = "<?php print $meta_description;?>" value = "<?php print $row->$meta_description;?>" />
                                      </td>
                                    </tr>
                                    <tr>
                                      <td  class="key">
                                        <?php echo _WOP_SHOP_META_KEYWORDS;?>
                                      </td>
                                      <td>
                                        <input type = "text" class = "inputbox" size = "60%" name = "<?php print $meta_keyword?>" value = "<?php print $row->$meta_keyword;?>" />
                                      </td>
                                    </tr>
                                    <?php $pkey = 'plugin_template_description_'.$lang->language; if ($this->$pkey){ print $this->$pkey;}?>
                                  </table>
                            <?php if ($this->multilang) : ?>
                                </div>
                            <?php endif; ?>
                        <?php } ?>
                <?php if ($this->multilang) : ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            <div id="tabImages" class="tab">
                <?php if ($row->category_image){ ?>
                    <div class = "wshop_quote" id="foto_category">
                       <div>
                           <div><img src = "<?php echo $config->image_category_live_path . '/' . $row->category_image?>" /></div>
                           <div class="link_delete_foto"><a href="#" onclick="if (confirm('<?php print _WOP_SHOP_DELETE_IMAGE;?>')) deleteFotoCategory('<?php echo $row->category_id?>');return false;"><img src="<?php print WOPSHOP_PLUGIN_URL?>assets/images/publish_r.png"> <?php print _WOP_SHOP_DELETE_IMAGE;?></a></div>
                       </div>
                    </div>
                    <?php } ?>
                    <div class="col100">

                    <table class="admintable" >
                      <tr>
                        <td class="key">
                          <?php echo _WOP_SHOP_IMAGE_SELECT;?>
                        </td>
                        <td>
                          <input type = "file" name = "category_image" />
                        </td>
                      </tr>
                      <tr>
                        <td class="key">
                          <?php echo _WOP_SHOP_IMAGE_THUMB_SIZE;?>
                        </td>
                        <td>
                          <div>
                          <input type = "radio" name = "size_im_category" id = "size_1" checked = "checked" onclick = "setDefaultSize(<?php echo $config->image_category_width; ?>,<?php echo $config->image_category_height; ?>, 'category')" value = "1" />
                          <label for = "size_1"><?php echo _WOP_SHOP_IMAGE_SIZE_1;?></label>
                          <div class="clear"></div>
                          </div>
                          <div>
                          <input type = "radio" name = "size_im_category" value = "3" id = "size_3" onclick = "setOriginalSize('category')" value = "3"/>
                          <label for = "size_3"><?php echo _WOP_SHOP_IMAGE_SIZE_3;?></label>
                          <div class="clear"></div>
                          </div>
                          <div>
                          <input type = "radio" name = "size_im_category" id = "size_2" onclick = "setManualSize('category')" value = "2" />
                          <label for = "size_2"><?php echo _WOP_SHOP_IMAGE_SIZE_2;?></label>
                          <div class="clear"></div>
                          </div>
                        </td>
                      </tr>
                      <tr>
                        <td class="key">
                          <?php echo _WOP_SHOP_IMAGE_WIDTH;?>
                        </td>
                        <td>
                          <input type = "text" id = "category_width_image" name = "category_width_image" class="inputbox" value = "<?php echo $config->image_category_width?>" disabled = "disabled" />
                        </td>
                      </tr>
                      <tr>
                        <td class="key">
                          <?php echo _WOP_SHOP_IMAGE_HEIGHT;?>
                        </td>
                        <td>
                          <input type = "text" id = "category_height_image" name = "category_height_image" class="inputbox" value = "<?php echo $config->image_category_height?>" disabled = "disabled" />
                        </td>
                      </tr>
                    </table>
                    <?php $pkey = 'plugin_template_img'; if ($this->$pkey){ print $this->$pkey;}?>    
                    </div>
                    <div class="clr"></div>
                    <br/><br/>
                    <div class="helpbox">
                       <div class="head"><?php echo _WOP_SHOP_ABOUT_UPLOAD_FILES;?></div>
                       <div class="text">
                               <?php print _WOP_SHOP_IMAGE_UPLOAD_EXT_INFO?><br/>
                           <?php print sprintf(_WOP_SHOP_SIZE_FILES_INFO, ini_get("upload_max_filesize"), ini_get("post_max_size"));?>
                       </div>
                   </div>
            </div>
        </div>
        <?php print $this->tmp_html_end?>
    </div>
</div>
<br>    
<div clas="submit">
    <p class="submit">
        <input class="button button-primary" type="submit" value="<?php echo _WOP_SHOP_ACTION_SAVE; ?>" name="submit">
        <a class="button" href="admin.php?page=categories"><?php echo _WOP_SHOP_BACK; ?></a>
    </p> 
</div>
    <input type="hidden" value="<?php echo $row->category_id; ?>" name="category_id">
    <input type="hidden" value="<?php echo $row->category_image; ?>" name="old_image">
    <?php wp_nonce_field('category_edit','name_of_nonce_field'); ?>
</form>