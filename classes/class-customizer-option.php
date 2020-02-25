<?php

/**
 * A class that extends WP_Customize_Setting so we can access
 * the protected updated method when importing options.
 *
 */
final class HDI_Customzer_Option extends WP_Customize_Setting {

    /**
     * Import an option value for this setting.
     *
     * @param mixed $value The option value.
     * @return void
     */
    public function import($value) {
        $this->update($value);
    }

}
