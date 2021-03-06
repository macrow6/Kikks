<?php
namespace codeneric\phmm\base\admin {
  require_once ($GLOBALS["HACKLIB_ROOT"]);
  use \codeneric\phmm\base\includes\Client;
  use \codeneric\phmm\base\includes\Project;
  use \codeneric\phmm\base\includes\Image;
  use \codeneric\phmm\enums\CannedEmailPlaceholders;
  use \codeneric\phmm\Configuration;
  use \codeneric\phmm\base\includes\Error;
  use \codeneric\phmm\Utils;
  use \codeneric\phmm\base\includes\ErrorSeverity;
  use \codeneric\phmm\base\globals\Superglobals;
  class FrontendHandler {
    const POST_EMPTY_TITLE_FILL = "No title";
    const ERROR_SCRIPT_HANDLE = "codeneric-phmm-err";
    public static function enqueue_styles() {
      $config = Configuration::get();
      \wp_enqueue_style(
        "codeneric-phmm-admin-css-fixes",
        $config[\hacklib_id("assets")][\hacklib_id("css")][\hacklib_id(
          "admin"
        )][\hacklib_id("fixes")],
        array(),
        null,
        "all"
      );
      if (!\hacklib_cast_as_boolean(self::is_our_business())) {
        return;
      }
      if (!\hacklib_cast_as_boolean(self::is_custom_post_edit_or_new())) {
        return;
      }
      $handle = "codeneric-phmm-admin-commons-css";
      \wp_enqueue_style(
        $handle,
        $config[\hacklib_id("assets")][\hacklib_id("css")][\hacklib_id(
          "admin"
        )][\hacklib_id("post")],
        array(),
        null,
        "all"
      );
    }
    private static function is_custom_post_edit_or_new() {
      $pagename = Superglobals::Globals("pagenow");
      return ($pagename === "post-new.php") || ($pagename === "post.php");
    }
    private static function safely_get_current_screen() {
      if (\hacklib_cast_as_boolean(\function_exists("get_current_screen"))) {
        return \get_current_screen();
      }
      return null;
    }
    private static function get_current_post_type() {
      $current_screen = self::safely_get_current_screen();
      if (!\hacklib_cast_as_boolean(\is_null($current_screen))) {
        return $current_screen->post_type;
      }
      return null;
    }
    private static function is_client_single_page() {
      return
        \hacklib_cast_as_boolean(self::is_custom_post_edit_or_new()) &&
        (self::get_current_post_type() ===
         Configuration::get()[\hacklib_id("client_post_type")]);
    }
    public static function is_project_single_page() {
      return
        \hacklib_cast_as_boolean(self::is_custom_post_edit_or_new()) &&
        (self::get_current_post_type() ===
         Configuration::get()[\hacklib_id("project_post_type")]);
    }
    private static function is_settings_page() {
      $current_screen = self::safely_get_current_screen();
      if (\hacklib_cast_as_boolean(\is_null($current_screen))) {
        return false;
      }
      return
        $current_screen->base ===
        (Configuration::get()[\hacklib_id("client_post_type")].
         "_page_".
         Settings::page_name);
    }
    private static function is_premium_page() {
      $current_screen = self::safely_get_current_screen();
      if (\hacklib_cast_as_boolean(\is_null($current_screen))) {
        return false;
      }
      return
        $current_screen->base ===
        (Configuration::get()[\hacklib_id("client_post_type")].
         "_page_".
         PremiumPage::page_name);
    }
    private static function is_support_page() {
      $current_screen = self::safely_get_current_screen();
      if (\hacklib_cast_as_boolean(\is_null($current_screen))) {
        return false;
      }
      return
        $current_screen->base ===
        (Configuration::get()[\hacklib_id("client_post_type")].
         "_page_".
         SupportPage::page_name);
    }
    private static function is_interaction_page() {
      $current_screen = self::safely_get_current_screen();
      if (\hacklib_cast_as_boolean(\is_null($current_screen))) {
        return false;
      }
      return
        $current_screen->base ===
        (Configuration::get()[\hacklib_id("client_post_type")].
         "_page_".
         InteractionsPage::page_name);
    }
    private static function is_our_business() {
      $current_post_type = self::get_current_post_type();
      $config = Configuration::get();
      return
        ($current_post_type === $config[\hacklib_id("client_post_type")]) ||
        ($current_post_type === $config[\hacklib_id("project_post_type")]);
    }
    private static function enqueue_filelist(
      $list,
      $globals,
      $dependencies = array()
    ) {
      $configuration = Configuration::get();
      foreach ($list as $index => $file) {
        $handle = $file;
        $isLast = \count($list) === ($index + 1);
        \wp_register_script($handle, $file, $dependencies, null, true);
        if (\hacklib_cast_as_boolean($isLast)) {
          $url =
            \plugins_url("/", $configuration[\hacklib_id("manifest_path")]);
          \wp_localize_script($handle, "codeneric_phmm_plugins_dir", $url);
          \wp_localize_script(
            $handle,
            "codeneric_phmm_admin_globals",
            \json_encode(self::get_general_admin_frontend_globals())
          );
          foreach ($globals as $name => $data) {
            \wp_localize_script($handle, $name, $data);
          }
        }
        \wp_enqueue_script($handle);
        \array_push($dependencies, $handle);
      }
    }
    public static function get_the_id() {
      $id = \get_the_ID();
      if (!\hacklib_cast_as_boolean(is_int($id))) {
        return null;
      }
      return $id;
    }
    public static function enqueue_scripts($hook) {
      $configuration = Configuration::get();
      $settings = Settings::getCurrentSettings();
      if ("%%DEMO_BUILD%%" === "true") {
        if (\hacklib_cast_as_boolean(self::should_start_product_tour())) {
          self::enqueue_filelist(
            $configuration[\hacklib_id("assets")][\hacklib_id("js")][\hacklib_id(
              "admin"
            )][\hacklib_id("product_tour")],
            array(
              "codeneric_phmm_product_tour_should_start" => \json_encode(
                self::should_start_product_tour()
              )
            )
          );
        }
      }
      if (\hacklib_cast_as_boolean(self::is_client_single_page())) {
        self::enqueue_filelist(
          $configuration[\hacklib_id("assets")][\hacklib_id("js")][\hacklib_id(
            "admin"
          )][\hacklib_id("client")],
          array(
            "codeneric_phmm_admin_client_globals" => \json_encode(
              self::get_admin_client_frontend_globals()
            ),
            "codeneric_phmm_admin_client_project_access_globals" =>
              \json_encode(
                self::get_admin_client_project_access_frontend_globals()
              )
          )
        );
      }
      if (\hacklib_cast_as_boolean(self::is_project_single_page())) {
        $scriptsrc =
          $configuration[\hacklib_id("assets")][\hacklib_id("js")][\hacklib_id(
            "admin"
          )][\hacklib_id("project")];
        $scripthandle =
          $configuration[\hacklib_id("plugin_name")]."-admin-project"; /* UNSAFE_EXPR */
        wp_enqueue_media();
        $admin_project_frontend_globals =
          self::get_admin_project_frontend_globals();
        if (\hacklib_cast_as_boolean(
              \is_null($admin_project_frontend_globals)
            )) {
          $admin_project_frontend_globals = array();
        }
        \wp_localize_script(
          $scripthandle,
          "CODENERIC_PHMM_ADMIN_PROJECT_GLOBALS",
          \json_encode($admin_project_frontend_globals)
        );
        self::enqueue_filelist(
          $configuration[\hacklib_id("assets")][\hacklib_id("js")][\hacklib_id(
            "admin"
          )][\hacklib_id("project")],
          array(
            "CODENERIC_PHMM_ADMIN_PROJECT_GLOBALS" => \json_encode(
              $admin_project_frontend_globals
            )
          ),
          array("media-upload")
        );
      }
      if (\hacklib_cast_as_boolean(self::is_settings_page())) { /* UNSAFE_EXPR */
        wp_enqueue_media();
        $watermarkImageID =
          $settings[\hacklib_id("watermark")][\hacklib_id("image_id")];
        $imageData = "";
        if (\hacklib_cast_as_boolean(is_int($watermarkImageID))) {
          $image = Image::get_image($watermarkImageID, false);
          if (!\hacklib_cast_as_boolean(\is_null($image))) {
            $imageData = \json_encode($image);
          }
        }
        self::enqueue_filelist(
          $configuration[\hacklib_id("assets")][\hacklib_id("js")][\hacklib_id(
            "admin"
          )][\hacklib_id("settings")],
          array(
            "codeneric_phmm_settings_globals" => \json_encode(
              self::get_settings_frontend_globals()
            ),
            "codeneric_phmm_current_settings" => \json_encode($settings),
            "codeneric_phmm_settings_preloaded_watermark_image" =>
              $imageData
          ),
          array("media-upload")
        );
      }
      if (\hacklib_cast_as_boolean(self::is_interaction_page())) {
        self::enqueue_filelist(
          $configuration[\hacklib_id("assets")][\hacklib_id("js")][\hacklib_id(
            "admin"
          )][\hacklib_id("interactions_page")],
          array(
            "codeneric_phmm_interactions_globals" => \json_encode(
              self::get_interactions_frontend_globals()
            )
          )
        );
      }
      if (\hacklib_cast_as_boolean(self::is_premium_page())) {
        self::enqueue_filelist(
          $configuration[\hacklib_id("assets")][\hacklib_id("js")][\hacklib_id(
            "admin"
          )][\hacklib_id("premium_page")],
          array(
            "codeneric_phmm_premiumpage_globals" => \json_encode(
              self::get_premium_page_globals()
            )
          ),
          array("media-upload")
        );
      }
      if (\hacklib_cast_as_boolean(self::is_support_page())) {
        self::enqueue_filelist(
          $configuration[\hacklib_id("assets")][\hacklib_id("js")][\hacklib_id(
            "admin"
          )][\hacklib_id("support_page")],
          array(
            "codeneric_phmm_supportpage_globals" => \json_encode(
              self::get_support_page_globals()
            )
          ),
          array()
        );
      }
    }
    public static function handle_analytics_enqueue() {
      if (!\hacklib_cast_as_boolean(self::is_our_business())) {
        return;
      }
      $settings = Settings::getCurrentSettings();
      $trackingID = Configuration::get()[\hacklib_id("ga_tracking_id")];
      $gTagSnippet =
        \hacklib_cast_as_boolean($settings[\hacklib_id("analytics_opt_in")])
          ? "<script async src='https://www.googletagmanager.com/gtag/js?id=".
          $trackingID.
          "'></script>"
          : "";
      $optOut =
        ($settings[\hacklib_id("analytics_opt_in")] === true)
          ? "window['ga-disable-".$trackingID."'] = false;"
          : "window['ga-disable-".$trackingID."'] = true;";
      $id = Utils::get_plugin_id();
      $hashedID = \md5($id);
      echo
        ($gTagSnippet.
         "\n      <script>\n        window.dataLayer = window.dataLayer || [];\n        function gtag() {\n          dataLayer.push(arguments);\n        }\n        gtag('js', new Date());\n        gtag('config', '".
         $trackingID.
         "', {\n          user_id: '".
         $hashedID.
         "',\n          custom_map: { dimension1: 'platform', dimension2: 'productType', 'anonymize_ip': true  }\n        });\n        ".
         $optOut.
         "\n      </script>\n      ")
      ;
    }
    public static function handle_placeholder_image_upload() {
      $optionName =
        Configuration::get()[\hacklib_id("default_thumbnail_id_option_key")];
      $id = \get_option($optionName, null);
      if (!\hacklib_cast_as_boolean(\is_null($id))) {
        $exists = \wp_attachment_is_image((int) $id);
        if (\hacklib_cast_as_boolean($exists)) {
          return;
        }
      }
      $imageUrl = \plugin_dir_path(__FILE__)."/../assets/img/placeholder.png";
      $upload = \wp_upload_bits(
        "phmm_project_placeholder.png",
        null,
        \file_get_contents($imageUrl)
      );
      $wp_filetype =
        \wp_check_filetype(\basename($upload[\hacklib_id("file")]), null);
      $wp_upload_dir = \wp_upload_dir();
      $attachment = array(
        "guid" =>
          $wp_upload_dir[\hacklib_id("baseurl")].
          \_wp_relative_upload_path($upload[\hacklib_id("file")]),
        "post_mime_type" => $wp_filetype[\hacklib_id("type")],
        "post_title" => \preg_replace(
          "/\\.[^.]+$/",
          "",
          \basename($upload[\hacklib_id("file")])
        ),
        "post_content" => "",
        "post_status" => "inherit"
      );
      $attach_id =
        \wp_insert_attachment($attachment, $upload[\hacklib_id("file")]);
      $attach_data = \wp_generate_attachment_metadata(
        $attach_id,
        $upload[\hacklib_id("file")]
      );
      \wp_update_attachment_metadata($attach_id, $attach_data);
      if (\hacklib_cast_as_boolean(is_int($attach_id))) {
        \update_option($optionName, $attach_id);
      }
    }
    public static function get_general_admin_frontend_globals() {
      $config = Configuration::get();
      $res = array(
        "base_url" => \get_site_url(),
        "ajax_url" => \admin_url("admin-ajax.php"),
        "author_id" => Utils::get_current_user_id(),
        "locale" => (string) \get_locale(),
        "wpps_url" => $config[\hacklib_id("wpps_url")],
        "canned_emails_supported_placeholders" => \array_values(
          CannedEmailPlaceholders::getValues()
        ),
        "canned_emails" => Settings::getCurrentSettings()[\hacklib_id(
          "canned_emails"
        )],
        "links" => array(
          "new_project" => \add_query_arg(
            array(
              "post_type" => $config[\hacklib_id("project_post_type")]
            ),
            \admin_url("post-new.php")
          ),
          "new_client" => \add_query_arg(
            array("post_type" => $config[\hacklib_id("client_post_type")]),
            \admin_url("post-new.php")
          ),
          "settings" => \add_query_arg(
            array(
              "post_type" => $config[\hacklib_id("client_post_type")],
              "page" => "options"
            ),
            \admin_url("edit.php")
          ),
          "support" => \add_query_arg(
            array(
              "post_type" => $config[\hacklib_id("client_post_type")],
              "page" => "support"
            ),
            \admin_url("edit.php")
          )
        )
      );
      return $res;
    }
    public static function strip_wp_posts($posts) {
      $whitelist = array("id", "post_title");
      return \array_map(
        function($post) {
          return array("id" => $post->ID, "title" => $post->post_title);
        },
        $posts
      );
    }
    public static function get_settings_frontend_globals() {
      $options = Settings::getCurrentSettings();
      $theme = \get_template_directory();
      $template_file = $theme."/".$options[\hacklib_id("page_template")];
      $current_template_exists = \file_exists($template_file);
      $pages = \get_page_templates();
      $templates = array();
      foreach ($pages as $key => $value) {
        $arr = array("templatename" => $key, "filename" => $value);
        $templates[] = $arr;
      }
      $current_theme = \wp_get_theme();
      $current_theme_name = $current_theme->offsetGet("Name");
      return array(
        "templates" => $templates,
        "current_template_exists" => $current_template_exists,
        "current_theme_name" => $current_theme_name,
        "pages" => self::strip_wp_posts(\get_pages())
      );
    }
    public static function get_interactions_frontend_globals() {
      $options = Settings::getCurrentSettings();
      $theme = \get_template_directory();
      $template_file = $theme."/".$options[\hacklib_id("page_template")];
      $current_template_exists = \file_exists($template_file);
      $pages = \get_page_templates();
      $templates = array();
      foreach ($pages as $key => $value) {
        $arr = array("templatename" => $key, "filename" => $value);
        $templates[] = $arr;
      }
      $current_theme = \wp_get_theme();
      $current_theme_name = $current_theme->offsetGet("Name");
      $clientIDs = Client::get_all_ids();
      $clients = \array_map(
        function($ID) {
          $client = Client::get_wp_user_from_client_id($ID);
          return array(
            "id" => $ID,
            "name" => \get_the_title($ID),
            "wp_user_name" =>
              \hacklib_cast_as_boolean(\is_null($client))
                ? null
                : $client->get("user_login"),
            "project_access" => Client::get_project_ids($ID)
          );
        },
        $clientIDs
      );
      $project_ids = Project::get_all_ids();
      $projects = \array_map(
        function($ID) {
          $post = \get_post($ID);
          $title = "";
          if (!\hacklib_cast_as_boolean(\is_null($post))) {
            $title = $post->post_title;
          }
          return array(
            "id" => $ID,
            "name" =>
              ($title === "")
                ? (self::POST_EMPTY_TITLE_FILL." #".$ID)
                : $title
          );
        },
        $project_ids
      );
      $project_ids_with_guest_access = array();
      foreach ($project_ids as $id) {
        $protec = Project::get_protection($id);
        if ((!\hacklib_cast_as_boolean(
               \is_null($protec[\hacklib_id("password")])
             )) ||
            (!\hacklib_cast_as_boolean($protec[\hacklib_id("private")]))) {
          $project_ids_with_guest_access[] = $id;
        }
      }
      $clients[] = array(
        "id" => 0,
        "name" => "Guest",
        "project_access" => $project_ids_with_guest_access,
        "wp_user_name" => null
      );
      return array("clients" => $clients, "projects" => $projects);
    }
    public static function get_premium_page_globals() {
      return array(
        "id" => Utils::get_plugin_id(),
        "has_premium_extension" => Configuration::get()[\hacklib_id(
          "has_premium_ext"
        )]
      );
    }
    public static function get_support_page_globals() {
      $email = \get_option("admin_email");
      if (!\hacklib_cast_as_boolean(is_string($email))) {
        $email = "";
      }
      $current_user = \wp_get_current_user();
      $name = $current_user->user_firstname." ".$current_user->user_lastname;
      return array("admin_email" => $email, "admin_name" => $name);
    }
    public static function get_admin_client_frontend_globals() {
      $id = \get_the_ID();
      \HH\invariant(
        is_int($id),
        "%s",
        new Error("Post is not set", array(array("id", $id)))
      );
      $client = Client::get($id);
      \HH\invariant(
        !\hacklib_cast_as_boolean(\is_null($client)),
        "%s",
        new Error("No client found with given id")
      );
      return $client;
    }
    public static function get_admin_project_frontend_globals() {
      $id = self::get_the_id();
      if (\hacklib_cast_as_boolean(\is_null($id))) {
        return null;
      }
      return Project::get_project_for_admin($id);
    }
    public static function get_admin_client_project_access_frontend_globals() {
      $id = \get_the_ID();
      $posts = \get_posts(
        array(
          "post_type" => Configuration::get()[\hacklib_id(
            "project_post_type"
          )],
          "post_status" => "any",
          "numberposts" => -1
        )
      );
      $configurations = array();
      foreach ($posts as $key => $project) {
        $configurations[$project->ID] =
          Project::get_configuration($project->ID);
      }
      if (\count($configurations) === 0) {
        $configurations = null;
      }
      return array(
        "projects" => self::populateMissingPostTitle($posts),
        "configurations" => $configurations
      );
    }
    private static function populateMissingPostTitle($posts) {
      return \array_map(
        function($post) {
          $post->post_title =
            ($post->post_title === "")
              ? (\__("No title", "photography-management")." #".$post->ID)
              : $post->post_title;
          return $post;
        },
        $posts
      );
    }
    public static function hide_admin_bar() {
      $settings = Settings::getCurrentSettings();
      if (($settings[\hacklib_id("hide_admin_bar")] === true) &&
          (!\hacklib_cast_as_boolean(\current_user_can("edit_posts")))) {
        \add_filter("show_admin_bar", "__return_false");
      }
    }
    public static function change_title_placeholder($title) {
      if (\hacklib_cast_as_boolean(self::is_client_single_page())) {
        return \__("Enter client name here", "phmm");
      }
      if (\hacklib_cast_as_boolean(self::is_project_single_page())) {
        return \__("Enter project name here", "phmm");
      }
      return $title;
    }
    private static function string_or_else($str, $alternative) {
      if ((!\hacklib_cast_as_boolean(is_string($str))) ||
          (\strlen($str) === 0)) {
        return $alternative;
      }
      return $str;
    }
    public static function define_client_table_columns($column_name) {
      $cols = array(
        "cb" => "<input type=\"checkbox\" />",
        "title" => \__("Name", "photography-management"),
        "projects" => \__("Projects", "photography-management"),
        "email" => \__("Email", "photography-management"),
        "shortcode" => \__("Shortcode", "photography-management"),
        "date" => \__("Date", "photography-management")
      );
      return $cols;
    }
    public static function define_project_table_columns() {
      $cols = array(
        "cb" => "<input type=\"checkbox\" />",
        "title" => \__("Project Name", "photography-management"),
        "categories" => \__("Categories", "photography-management"),
        "shortcode" => \__("Shortcode", "photography-management"),
        "date" => \__("Date", "photography-management")
      );
      return $cols;
    }
    public static function fill_project_columns($column, $post_id) {
      if ($column === "shortcode") {
        echo
          ("<code>[".
           \codeneric\phmm\base\frontend\Shortcodes::GALLERY.
           " id=\"".
           $post_id.
           "\"]</code>")
        ;
      }
    }
    public static function fill_client_columns($column, $post_id) {
      $post = \get_post($post_id);
      $emptyDash = "\342\200\224";
      $editLink = \get_edit_post_link($post_id);
      \HH\invariant(
        $post instanceof \WP_Post,
        "%s",
        new Error("Could not get post from post_id")
      );
      \HH\invariant(
        is_string($editLink),
        "%s",
        new Error("Could not get post edit link from post_id")
      );
      switch ($column) {
        case "shortcode":
          echo
            ("<code>[".
             \codeneric\phmm\base\frontend\Shortcodes::CLIENT.
             " id=\"".
             $post_id.
             "\"]</code>")
          ;
          break;
        case "email":
          $user = Client::get_wp_user_from_client_id($post_id);
          $email = \hacklib_nullsafe($user)->get("user_email");
          if (\hacklib_cast_as_boolean(\is_null($email)) || ($email === "")) {
            echo ($emptyDash);
          } else {
            echo ($email);
          }
          break;
        case "projects":
          {
            $projects = Client::get_project_wp_posts($post_id);
            if (\count($projects) === 0) {
              echo ($emptyDash);
            } else {
              foreach ($projects as $i => $project) {
                $editLink = \get_edit_post_link($project->ID);
                \HH\invariant(
                  is_string($editLink),
                  "%s",
                  new Error(
                    "Could not get project post edit link from post_id"
                  )
                );
                echo
                  ("<strong><a class=\"row-title post-edit-link\" href=\"".
                   $editLink.
                   "\">".
                   self::string_or_else(
                     $project->post_title,
                     \__("Unnamed project", "photography-management")
                   ).
                   "</a></strong>")
                ;
                if (($i + 1) < \count($projects)) {
                  echo (", ");
                }
              }
            }
            break;
          }
      }
      return;
    }
    public static function warn_if_page_template_not_exists() {
      $settings = Settings::getCurrentSettings();
      $chosenTemplate = false;
      $chosenTemplate = $settings[\hacklib_id("page_template")];
      $theme = \get_template_directory();
      $template_file = $theme."/".$chosenTemplate;
      if (($chosenTemplate !== "phmm-legacy") &&
          ($chosenTemplate !== "phmm-theme-default") &&
          (!\hacklib_cast_as_boolean(\file_exists($template_file)))) {
        $editurl = \admin_url("edit.php");
        $link = \add_query_arg(
          array("post_type" => "client", "page" => "options"),
          $editurl
        );
        $class = "notice notice-error";
        $message =
          \sprintf(
            \__(
              "The chosen page template in Photography Management -> Settings does not exist. Have you changed your theme recently? <br />Please <a href=\"%s\">update</a> the option.",
              "phmm"
            ),
            $link
          );
        $submessage = ""; /* UNSAFE_EXPR */
        printf(
          "<div class=\"".
          $class.
          "\">\n        <h3>Photography Management</h3>\n        <p><strong>".
          $message.
          "</strong></p><p>".
          $submessage.
          "</p></div>"
        );
      }
    }
    public static function get_loading_spinner_html() {
      return
        "<div class='cc-phmm-spinner' style=\"background:url('images/spinner.gif') no-repeat;background-size: 20px 20px;vertical-align: middle;margin: 0 auto;height: 20px;width: 20px;display:block;\"></div>";
    }
    public static function render_client_information_meta_box() {
      \wp_nonce_field("my_meta_box_nonce", "meta_box_nonce");
      echo
        ("<div id=\"cc_phmm_client_information\">".
         self::get_loading_spinner_html().
         "</div>")
      ;
    }
    public static function render_client_project_access_meta_box() {
      \wp_nonce_field("my_meta_box_nonce", "meta_box_nonce");
      echo
        ("<div id=\"cc_phmm_client_project_access\">".
         self::get_loading_spinner_html().
         "</div>")
      ;
    }
    public static function render_project_gallery_meta_box() {
      \wp_nonce_field("my_meta_box_nonce", "meta_box_nonce");
      echo
        ("<div id=\"cc_phmm_project_gallery\">".
         self::get_loading_spinner_html().
         "</div>")
      ;
    }
    public static function render_project_configuration_meta_box() {
      \wp_nonce_field("my_meta_box_nonce", "meta_box_nonce");
      echo
        ("<div id=\"cc_phmm_project_configuration\">".
         self::get_loading_spinner_html().
         "</div>")
      ;
    }
    public static function render_project_thumbnail_meta_box() {
      \wp_nonce_field("my_meta_box_nonce", "meta_box_nonce");
      echo
        ("<div id=\"cc_phmm_project_thumbnail\">".
         self::get_loading_spinner_html().
         "</div>")
      ;
    }
    public static function remove_post_links_for_custom_post_types($html) {
      $config = Configuration::get();
      $postType = \get_post_type();
      if (!\hacklib_cast_as_boolean(is_string($postType))) {
        return $html;
      }
      if (($postType !== $config[\hacklib_id("client_post_type")]) &&
          ($postType !== $config[\hacklib_id("project_post_type")])) {
        return $html;
      }
      return "";
    }
    public static function should_start_product_tour() {
      $config = Configuration::get();
      $option_product_tour_started =
        $config[\hacklib_id("option_product_tour_started")];
      $product_tour_started = \get_option($option_product_tour_started);
      if ($product_tour_started === false) {
        return true;
      }
      return false;
    }
    public static function set_product_tour_finished() {
      $config = Configuration::get();
      $option_product_tour_started =
        $config[\hacklib_id("option_product_tour_started")];
      \update_option($option_product_tour_started, \time());
    }
  }
}
