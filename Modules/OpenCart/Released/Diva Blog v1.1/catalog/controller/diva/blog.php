<?php
class ControllerDivaBlog extends Controller
{
    public function index() {
        $this->load->language('diva/blog');

        $this->load->model('diva/blog');
        $this->load->model('tool/image');

        if (isset($this->request->get['filter'])) {
            $filter = $this->request->get['filter'];
        } else {
            $filter = '';
        }

        if (isset($this->request->get['sort'])) {
            $sort = $this->request->get['sort'];
        } else {
            $sort = 'p.sort_order';
        }

        if (isset($this->request->get['order'])) {
            $order = $this->request->get['order'];
        } else {
            $order = 'ASC';
        }

        if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
        } else {
            $page = 1;
        }

        if (isset($this->request->get['limit'])) {
            $limit = $this->request->get['limit'];
        } else {
            $limit = $this->config->get('module_dvblog_post_limit');
        }

        $this->document->setTitle($this->config->get('module_dvblog_meta_title'));
        $this->document->setDescription($this->config->get('module_dvblog_meta_description'));
        $this->document->setKeywords($this->config->get('module_dvblog_meta_keyword'));
        $this->document->addLink($this->url->link('diva/blog'), '');

        $data['heading_title'] = $this->config->get('module_dvblog_meta_title');

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home')
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_blog'),
            'href' => $this->url->link('diva/blog')
        );

        $url = '';

        if (isset($this->request->get['filter'])) {
            $url .= '&filter=' . $this->request->get['filter'];
        }

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        if (isset($this->request->get['limit'])) {
            $url .= '&limit=' . $this->request->get['limit'];
        }

        $filter_data = array(
            'filter_filter'      => $filter,
            'sort'               => $sort,
            'order'              => $order,
            'start'              => ($page - 1) * $limit,
            'limit'              => $limit
        );

        $post_total = $this->model_diva_blog->getTotalPosts($filter_data);

        $results = $this->model_diva_blog->getPosts($filter_data);

        $width = (int) $this->config->get('module_dvblog_blog_width');
        $height = (int) $this->config->get('module_dvblog_blog_height');

        $data['posts'] = array();

        foreach ($results as $result) {
            $image = $this->model_tool_image->resize($result['image'], $width, $height);

            $data['posts'][] = array(
                'post_id'     => $result['post_id'],
                'name'        => $result['name'],
                'author'	  => $result['author'],
                'image'		  => $image,
                'date_added'  => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
                'intro_text'  => html_entity_decode($result['intro_text'], ENT_QUOTES, 'UTF-8'),
                'href'        => $this->url->link('diva/blog/post', 'post_id=' . $result['post_id'] . $url)
            );
        }

        $url = '';

        if (isset($this->request->get['filter'])) {
            $url .= '&filter=' . $this->request->get['filter'];
        }

        if (isset($this->request->get['limit'])) {
            $url .= '&limit=' . $this->request->get['limit'];
        }

        $data['sorts'] = array();

        $data['sorts'][] = array(
            'text'  => $this->language->get('text_default'),
            'value' => 'p.sort_order-ASC',
            'href'  => $this->url->link('diva/blog', '&sort=p.sort_order&order=ASC' . $url)
        );

        $url = '';

        if (isset($this->request->get['filter'])) {
            $url .= '&filter=' . $this->request->get['filter'];
        }

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        $data['limits'] = array();

        $limits = array_unique(array($this->config->get('module_dvblog_post_limit'), 50, 75, 100));

        sort($limits);

        foreach($limits as $value) {
            $data['limits'][] = array(
                'text'  => $value,
                'value' => $value,
                'href'  => $this->url->link('blog/blog', $url . '&limit=' . $value)
            );
        }

        $url = '';

        if (isset($this->request->get['filter'])) {
            $url .= '&filter=' . $this->request->get['filter'];
        }

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        if (isset($this->request->get['limit'])) {
            $url .= '&limit=' . $this->request->get['limit'];
        }

        $pagination = new Pagination();
        $pagination->total = $post_total;
        $pagination->page = $page;
        $pagination->limit = $limit;
        $pagination->url = $this->url->link('diva/blog', $url . '&page={page}');

        $data['pagination'] = $pagination->render();
        $data['results'] = sprintf($this->language->get('text_pagination'), ($post_total) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($post_total - $limit)) ? $post_total : ((($page - 1) * $limit) + $limit), $post_total, ceil($post_total / $limit));

        $data['sort'] = $sort;
        $data['order'] = $order;
        $data['limit'] = $limit;

        $data['column_left'] = $this->load->controller('common/column_left');
        $data['column_right'] = $this->load->controller('common/column_right');
        $data['content_top'] = $this->load->controller('common/content_top');
        $data['content_bottom'] = $this->load->controller('common/content_bottom');
        $data['footer'] = $this->load->controller('common/footer');
        $data['header'] = $this->load->controller('common/header');

        $this->response->setOutput($this->load->view('diva/blog/list', $data));
    }

    public function post() {
        $this->load->language('diva/blog');

        $this->load->model('diva/blog');
        $this->load->model('tool/image');

        if (isset($this->request->get['post_id'])) {
            $post_id = (int)$this->request->get['post_id'];
        } else {
            $post_id = 0;
        }

        $post_info = $this->model_diva_blog->getPost($post_id);

        if ($post_info) {
            $data['breadcrumbs'] = array();

            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('text_home'),
                'href' => $this->url->link('common/home')
            );

            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('text_blog'),
                'href' => $this->url->link('diva/blog')
            );

            $data['breadcrumbs'][] = array(
                'text' => $post_info['name'],
                'href' => $this->url->link('diva/blog/post', '&post_id=' . $this->request->get['post_id'])
            );

            $this->document->setTitle($post_info['meta_title']);
            $this->document->setDescription($post_info['meta_description']);
            $this->document->setKeywords($post_info['meta_keyword']);
            $this->document->addLink($this->url->link('diva/blog/post', 'post_id=' . $this->request->get['post_id']), 'canonical');

            $data['heading_title'] = $post_info['name'];
            $data['author'] = $post_info['author'];
            $data['date'] = date($this->language->get('date_format_short'), strtotime($post_info['date_added']));
            $data['post_id'] = (int) $this->request->get['post_id'];
            $data['description'] = html_entity_decode($post_info['description'], ENT_QUOTES, 'UTF-8');

            if($this->config->get('module_dvblog_post_width')) {
                $image_size_width = (int) $this->config->get('module_dvblog_post_width');
            } else {
                $image_size_width = 200;
            }

            if($this->config->get('module_dvblog_post_height')) {
                $image_size_height = (int) $this->config->get('module_dvblog_post_height');
            } else {
                $image_size_height = 200;
            }

            $data['image'] = $this->model_tool_image->resize($post_info['image'], $image_size_width, $image_size_height);

            $data['column_left'] = $this->load->controller('common/column_left');
            $data['column_right'] = $this->load->controller('common/column_right');
            $data['content_top'] = $this->load->controller('common/content_top');
            $data['content_bottom'] = $this->load->controller('common/content_bottom');
            $data['footer'] = $this->load->controller('common/footer');
            $data['header'] = $this->load->controller('common/header');

            $this->response->setOutput($this->load->view('diva/blog/post', $data));
        } else {
            $url = '';

            if (isset($this->request->get['path'])) {
                $url .= '&path=' . $this->request->get['path'];
            }

            if (isset($this->request->get['filter'])) {
                $url .= '&filter=' . $this->request->get['filter'];
            }

            if (isset($this->request->get['search'])) {
                $url .= '&search=' . $this->request->get['search'];
            }

            if (isset($this->request->get['description'])) {
                $url .= '&description=' . $this->request->get['description'];
            }

            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            if (isset($this->request->get['limit'])) {
                $url .= '&limit=' . $this->request->get['limit'];
            }

            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('text_error'),
                'href' => $this->url->link('diva/blog/post', $url . '&post_id=' . $post_id)
            );

            $this->document->setTitle($this->language->get('text_error'));

            $data['heading_title'] = $this->language->get('text_error');

            $data['text_error'] = $this->language->get('text_error');

            $data['button_continue'] = $this->language->get('button_continue');

            $data['continue'] = $this->url->link('common/home');

            $this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . ' 404 Not Found');

            $data['column_left'] = $this->load->controller('common/column_left');
            $data['column_right'] = $this->load->controller('common/column_right');
            $data['content_top'] = $this->load->controller('common/content_top');
            $data['content_bottom'] = $this->load->controller('common/content_bottom');
            $data['footer'] = $this->load->controller('common/footer');
            $data['header'] = $this->load->controller('common/header');

            $this->response->setOutput($this->load->view('error/not_found', $data));
        }
    }
}