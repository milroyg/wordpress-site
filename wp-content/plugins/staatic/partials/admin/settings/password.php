<?php

namespace Staatic\Vendor;

/**
 * @var Staatic\WordPress\Setting\SettingInterface $setting
 * @var array $attributes
 */
?>

<fieldset>
    <legend class="screen-reader-text"><span><?php 
echo \esc_html($setting->label());
?></span></legend>
    <?php 
if (isset($attributes['composed'])) {
    ?>
        <label for="<?php 
    echo \esc_attr($setting->name());
    ?>"><?php 
    echo \esc_html($setting->label());
    ?></label><br>
    <?php 
} else {
    ?>
        <label for="<?php 
    echo \esc_attr($setting->name());
    ?>">
    <?php 
}
?>
        <input
            type="password"
            class="regular-text code"
            name="<?php 
echo \esc_attr($setting->name());
?>"
            id="<?php 
echo \esc_attr($setting->name());
?>"
            value="<?php 
echo \esc_attr($setting->value());
?>"
            <?php 
echo $attributes['disableAutocomplete'] ?? \false ? 'autocomplete="new-password"' : '';
?>
            <?php 
echo $attributes['disabled'] ?? \false ? 'disabled="disabled"' : '';
?>
        >
        <?php 
if (isset($attributes['locked'])) {
    ?>
            <span class="staatic-adornment dashicons dashicons-lock" title="<?php 
    echo \esc_attr($attributes['locked']);
    ?>"></span>
        <?php 
}
?>
    <?php 
if (!isset($attributes['composed'])) {
    ?>
        </label>
    <?php 
}
?>
    <?php 
if ($setting->description()) {
    ?>
        <p class="description">
            <?php 
    echo \wp_kses_post($setting->description());
    ?>
        </p>
    <?php 
}
?>
</fieldset>
<?php 
