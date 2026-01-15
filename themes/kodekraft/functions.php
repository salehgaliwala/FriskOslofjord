<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

// BEGIN ENQUEUE PARENT ACTION
// AUTO GENERATED - Do not modify or remove comment markers above or below:

if ( !function_exists( 'chld_thm_cfg_locale_css' ) ):
    function chld_thm_cfg_locale_css( $uri ){
        if ( empty( $uri ) && is_rtl() && file_exists( get_template_directory() . '/rtl.css' ) )
            $uri = get_template_directory_uri() . '/rtl.css';
        return $uri;
    }
endif;
add_filter( 'locale_stylesheet_uri', 'chld_thm_cfg_locale_css' );
         
if ( !function_exists( 'child_theme_configurator_css' ) ):
    function child_theme_configurator_css() {
        wp_enqueue_style( 'chld_thm_cfg_child', trailingslashit( get_stylesheet_directory_uri() ) . 'style.css', array( 'hello-elementor','hello-elementor','hello-elementor-theme-style','hello-elementor-header-footer' ) );
    }
endif;
add_action( 'wp_enqueue_scripts', 'child_theme_configurator_css', 10 );

// END ENQUEUE PARENT ACTION

add_action( 'wp_footer', function() {
if ( ! defined( 'ELEMENTOR_VERSION' ) ) {
return;
}
?>
<script>

window.addEventListener('elementor/frontend/init', function () {

    let desktopOffset = 100;
    let tabletOffset = 90;
    let mobileOffset = 70;

    let getOffset = function () {
        let offset = desktopOffset;

        if (window.innerWidth <= 1024) {
            offset = tabletOffset;
        }

        if (window.innerWidth <= 767) {
            offset = mobileOffset;
        }

        return offset;
    }
    elementorFrontend.hooks.addFilter('frontend/handlers/menu_anchor/scroll_top_distance', function (scrollTop) {
        return scrollTop - getOffset();
    });
});
</script>
<?php
} );

function load_and_parse_csv() {
    $csv_file = get_stylesheet_directory() . '/contactv1.csv'; // Adjust the path to your CSV file
    
    $csv_data = array_map('str_getcsv', file($csv_file));
    $csv_headers = array_shift($csv_data);

    $data = [];
    foreach ($csv_data as $row) {
        // Ensure UTF-8 encoding
        $row = array_map(function($field) {
            return mb_convert_encoding($field, 'UTF-8', 'auto');
        }, $row);

        $row = array_combine($csv_headers, $row);
        $data[mb_convert_encoding($row['parent'], 'UTF-8', 'auto')][] = [
            'child' => mb_convert_encoding($row['child'], 'UTF-8', 'auto'),
            'value' => mb_convert_encoding($row['value'], 'UTF-8', 'auto'),
            'placeholder' => mb_convert_encoding($row['placeholder'], 'UTF-8', 'auto'),
            'custom_text' => mb_convert_encoding($row['custom_text'], 'UTF-8', 'auto')
        ];
       
    }
    return $data;
}


function enqueue_custom_scripts() {
    wp_enqueue_script('custom-csv-script',get_stylesheet_directory_uri() . '/custom-script.js', ['jquery'], null, true);

    // Load and parse CSV data
    $data = load_and_parse_csv(); // Assuming this function returns the parsed CSV data as an array

    // Pass data to the JavaScript file
    wp_localize_script('custom-csv-script', 'csvData', $data);
}
add_action('wp_enqueue_scripts', 'enqueue_custom_scripts');


remove_action( 'wpcf7_swv_create_schema', 'wpcf7_swv_add_select_enum_rules', 20, 2 );


function dynamic_email_recipient($contact_form) {
    $form_id = $contact_form->id();
 
    if ($form_id == '1426') { // Replace with your form ID
        add_filter('wpcf7_mail_components', function($components) {
            if (isset($_POST['butikk']) && filter_var($_POST['butikk'], FILTER_VALIDATE_EMAIL)) {
                $components['recipient'] = sanitize_email($_POST['butikk']);
            }
      
            return $components;
        });
    }
}
add_action('wpcf7_before_send_mail', 'dynamic_email_recipient');
add_filter( 'wpsl_cpt_info_window_meta_fields', 'custom_cpt_info_window_meta_fields', 10, 2 );

function custom_cpt_info_window_meta_fields( $meta_fields, $store_id ) {
  $url = wp_get_attachment_url( get_post_thumbnail_id( $store_id) );
    $meta_fields['post_thumbnail_url'] = $url;

    return $meta_fields;
}

add_filter( 'wpsl_cpt_info_window_template', 'custom_cpt_info_window_template' );

function custom_cpt_info_window_template() {
    
    $cpt_info_window_template = '<div class="wpsl-info-window">' . "\r\n";
    $cpt_info_window_template .= "\t\t\t" . '<% if ( post_thumbnail_url ) { %>' . "\r\n";
    $cpt_info_window_template .= "\t\t\t" . '<img src="<%= post_thumbnail_url %>"/>' . "\r\n";
    $cpt_info_window_template .= "\t\t\t" . '<% } %>' . "\r\n";
    $cpt_info_window_template .= "\t\t" . '<p class="wpsl-no-margin">' . "\r\n";
    $cpt_info_window_template .= "\t\t\t" .  wpsl_store_header_template( 'wpsl_map' ) . "\r\n";   
    $cpt_info_window_template .= "\t\t\t" . '<span><%= address %></span>' . "\r\n";
    $cpt_info_window_template .= "\t\t\t" . '<% if ( address2 ) { %>' . "\r\n";
    $cpt_info_window_template .= "\t\t\t" . '<span><%= address2 %></span>' . "\r\n";
    $cpt_info_window_template .= "\t\t\t" . '<% } %>' . "\r\n";
    $cpt_info_window_template .= "\t\t\t" . '<span>' . wpsl_address_format_placeholders() . '</span>' . "\r\n"; 
    $cpt_info_window_template .= "\t\t\t" . '<span class="wpsl-country"><%= country %></span>' . "\r\n"; 
    $cpt_info_window_template .= "\t\t" . '</p>' . "\r\n";    

    
    return $cpt_info_window_template;
}

// Add Shortcode - M
function ajax_category_pills_shortcode() {
    ob_start();
    global $wpdb;

    // Get Categories
    $categories = get_categories();

    // Get Months
    $months = $wpdb->get_results("
        SELECT DISTINCT YEAR(post_date) AS year, MONTH(post_date) AS month
        FROM $wpdb->posts
        WHERE post_type = 'post' AND post_status = 'publish'
        ORDER BY post_date DESC
    ");
    ?>
    <div id="category-pills">
        <div class="category-filter-container">
            <select id="category-dropdown">
                <option value="all">Alle kategorier</option>
                <?php
                foreach ($categories as $category) {
                    echo '<option value="' . esc_attr($category->slug) . '">' . esc_html($category->name) . '</option>';
                }
                ?>
            </select>
            <select id="month-dropdown">
                <option value="all">Alle datoer</option>
                <?php
                if ($months) {
                    foreach ($months as $m) {
                        $date_obj = DateTime::createFromFormat('!m', $m->month);
                        $month_name = date_i18n('F', $date_obj->getTimestamp());
                        $value = $m->year . '-' . str_pad($m->month, 2, '0', STR_PAD_LEFT);
                        echo '<option value="' . esc_attr($value) . '">' . esc_html($month_name . ' ' . $m->year) . '</option>';
                    }
                }
                ?>
            </select>
        </div>
        <div id="posts-container"></div>
        <div id="pagination-container"></div>
    </div>
<script>
 jQuery(document).ready(function ($) {

    function loadPosts(page = 1, shouldScroll = false) {
        const category = $('#category-dropdown').val();
        const month = $('#month-dropdown').val();

        const container = $('#posts-container');
        const pagination = $('#pagination-container');
        container.html('Loading...');
        pagination.html('');

        $.ajax({
            url: '<?php echo admin_url("admin-ajax.php"); ?>',
            method: 'POST',
            data: {
                action: 'load_posts_by_category',
                category: category,
                month: month,
                page: page
            },
            success: function (response) {
                const parsed = JSON.parse(response);
                container.html(parsed.posts);
                pagination.html(parsed.pagination);

                if (shouldScroll) {
                    setTimeout(() => {
                        const targetElement = document.querySelector('#category-pills');
                        if (targetElement) {
                            targetElement.scrollIntoView({
                                behavior: 'smooth',
                                block: 'start'
                            });
                        }
                    }, 50);
                }
            }
        });
    }

    $('#category-dropdown, #month-dropdown').on('change', function () {
        loadPosts(1, true);
    });

    $('#pagination-container').on('click', '.page-number', function (e) {
        e.preventDefault();
        const page = $(this).data('page');
        loadPosts(page, true);
    });

    loadPosts();
});
</script>
    <style>
        #category-pills {
            text-align: center;
        }
        .category-filter-container {
            margin-bottom: 20px;
            display: flex;
            justify-content: center;
            gap: 20px;
            flex-wrap: wrap;
        }
        #category-dropdown, #month-dropdown {
            padding: 10px;
            font-size: 16px;
            width: 100%;
            max-width: 300px;
            border: 1px solid #166D47;
            color: #166D47;
            background-color: transparent;
            cursor: pointer;
        }
        .post {
            margin: 15px;           
            text-align: left;
            
        }
        .post img {
            width: 100%;
            height: auto;
        }
        .post-meta, .post-date {
            font-size: 14px;
            color: #888;
            margin-bottom: 10px;
        }
        .post-sub-title{
              color: #166D47;
              margin-bottom: 15px;
        }
        .post-title a{
            color:#166D47;
            font-family: "Eudoxus", Sans-serif;
            font-size: 20px;
            font-weight: 600;
            text-decoration: none;
          
        }
        .post-title {
            margin: 25px 0;
            -ms-word-wrap: break-word;
            word-wrap: break-word;
        }
        #posts-container .post img:hover
            {
                transition: transform 0.25s;
                transform:scale(1.1)
            }

        .post-excerpt {
            font-size: 16px;
            color: #333;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
        }
        .post:hover .post-excerpt
        {
            opacity:0.8
        }
        .page-number{font-size:16px;margin:0px 10px}
        .read-more{margin-top:25px}
        #posts-container a{display: block;overflow: hidden;}
        @media (min-width: 768px) {
            #posts-container {
                display: flex;
                flex-wrap: wrap;
                justify-content: space-between;
            }
            .post {
                flex: 1 1 calc(33.333% - 30px);
                box-sizing: border-box;
                flex-grow: 0;
            }
            
        }
        @media (max-width: 767px) {
            .page-number{font-size:22px;margin:0px 10px}
            .post {
                flex: 1 1 100%;
            }
        }
        #posts-container .post a{text-decoration: none;}
        .post-date{margin-bottom:20px}
    </style>
    <?php
    return ob_get_clean();
}
add_image_size('blog_square','400','400', true);
add_shortcode('category_pills', 'ajax_category_pills_shortcode');

// AJAX Handler 
function load_posts_by_category() {
    $category = $_POST['category'] ?? 'all';
    $month = $_POST['month'] ?? 'all';
    $paged = $_POST['page'] ?? 1;
    $args = [
        'post_type' => 'post',
        'posts_per_page' => 12,
        'paged' => $paged,
        'status' => 'publish',
        'category__not_in' => [1], // Exclude "Uncategorized" (ID = 1 by default)
         'orderby' => 'date',
         'order'   => 'DESC',
    ];

    if ($category !== 'all') {
        $args['category_name'] = sanitize_text_field($category);
    }

    if ($month !== 'all') {
        $parts = explode('-', $month);
        if (count($parts) === 2) {
            $args['date_query'] = [
                [
                    'year'  => $parts[0],
                    'month' => $parts[1],
                ],
            ];
        }
    }

    $query = new WP_Query($args);

    $posts_html = '';
    while ($query->have_posts()) {
        $query->the_post();
        $categories = get_the_category();
        $category_links = [];
        foreach ($categories as $cat) {
            $category_links[] = esc_html($cat->name);
        }

        $posts_html .= '<div class="post">';
        $posts_html .= '<a href="'.get_the_permalink().'">';
        $posts_html .= get_the_post_thumbnail(get_the_ID(), 'blog_square');
        $posts_html .= '</a>';
      ////  $posts_html .= '<div class="post-meta">' . get_the_date('d. F Y') . ' / ' . implode(', ', $category_links) . '</div>';
        $posts_html .= '<div class="post-title"><a href="'.get_the_permalink().'">' . get_the_title() . '</a></div>';
        $posts_html .= '<div class="post-sub-title">' . get_post_meta(get_the_ID(), '_leverandor', true) . '</div>';
        $posts_html .= '<div class="post-date">📅 ' . get_the_date('d. F Y') . '</div>';
        $posts_html .= '<div class="post-excerpt">' . get_the_excerpt() . '</div>'; ;
        $posts_html .= '<a href="'.get_the_permalink().'" class="read-more">'.__('Les mer →').'</a>';
        $posts_html .= '</div>';
    }
    wp_reset_postdata();

    $pagination_html = paginate_links([
        'total' => $query->max_num_pages,
        'current' => $paged,
        'type' => 'array',
    ]);

    $pagination_links = '';
    if ($pagination_html) {
        foreach ($pagination_html as $page_link) {
            // Ensure there is no nested anchor tag issue
            $clean_page_link = preg_replace('/<a[^>]*>(.*?)<\/a>/', '$1', $page_link);

            // Extract page number from link
            preg_match('/page\/([0-9]+)/', $page_link, $matches);
            $page_number = $matches[1] ?? 1;

            $pagination_links .= '<a href="#" class="page-number" data-page="' . esc_attr($page_number) . '">' . $clean_page_link . '</a>';
        }
    }

    echo json_encode(["posts" => $posts_html, "pagination" => $pagination_links]);
    wp_die();
}
add_action('wp_ajax_load_posts_by_category', 'load_posts_by_category');
add_action('wp_ajax_nopriv_load_posts_by_category', 'load_posts_by_category');   

function add_leverandor_meta_box() {
    add_meta_box(
        'leverandor_meta_box',         // ID
        'Leverandør',                  // Title
        'render_leverandor_meta_box', // Callback function
        'post',                        // Post type
        'side',                        // Context (normal, side, etc.)
        'default'                      // Priority
    );
}
add_action('add_meta_boxes', 'add_leverandor_meta_box');

function render_leverandor_meta_box($post) {
    // Add a nonce field for security
    wp_nonce_field('save_leverandor_meta_box', 'leverandor_meta_box_nonce');

    // Retrieve current value
    $value = get_post_meta($post->ID, '_leverandor', true);

    // Output the text field
    echo '<label for="leverandor_field">Leverandør:</label>';
    echo '<input type="text" id="leverandor_field" name="leverandor_field" value="' . esc_attr($value) . '" style="width:100%;" />';
}

function save_leverandor_meta_box($post_id) {
    // Check nonce
    if (!isset($_POST['leverandor_meta_box_nonce']) || 
        !wp_verify_nonce($_POST['leverandor_meta_box_nonce'], 'save_leverandor_meta_box')) {
        return;
    }

    // Prevent autosave overwrite
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

    // Check user permissions
    if (!current_user_can('edit_post', $post_id)) return;

    // Sanitize and save the field
    if (isset($_POST['leverandor_field'])) {
        $value = sanitize_text_field($_POST['leverandor_field']);
        update_post_meta($post_id, '_leverandor', $value);
    }
}
add_action('save_post', 'save_leverandor_meta_box');