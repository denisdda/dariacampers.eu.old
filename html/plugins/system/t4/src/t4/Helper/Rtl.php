<?php
namespace T4\Helper;

class Rtl
{
    public static function render()
    {
        //
        $ltrfile = Path::findInTheme('css/template.css');
        $rtlfile = T4PATH_MEDIA . '/css/rtl.css';

        if (!is_file($rtlfile) || filemtime($rtlfile) < filemtime($ltrfile)) {
            require_once T4PATH . '/vendor/autoload.php';
            require_once T4PATH . '/vendor/rtlcss/rtlcss.php';

            $ltrcss = file_get_contents($ltrfile);
            $parser = new \Sabberworm\CSS\Parser($ltrcss);
            $tree = $parser->parse();
            $rtlcss = new \MoodleHQ\RTLCSS\RTLCSS($tree);
            $rtlcss->flip();
            $rtlcss = $tree->render();

            \JFile::write($rtlfile, $rtlcss);
        }

        return T4PATH_MEDIA_URI . '/css/rtl.css';
    }
}
