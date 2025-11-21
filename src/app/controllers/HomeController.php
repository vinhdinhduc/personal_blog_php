 <?php

    /**
     * Home Controller
     * Xử lý trang chủ và listing
     */

    require_once __DIR__ . '/BaseController.php';
    require_once __DIR__ . '/../models/PostModel.php';
    require_once __DIR__ . '/../models/CategoryModel.php';
    require_once __DIR__ . '/../models/TagModel.php';

    class HomeController extends BaseController
    {

        /**
         * Trang chủ - danh sách bài viết
         * @param int $page
         */
        public function index($page = 1)
        {
            $postModel = new PostModel();
            $categoryModel = new CategoryModel();
            $tagModel = new TagModel();

            $perPage = 10;
            $page = max(1, (int)$page);

            //Lấy danh sách bài viết published
            $posts = $postModel->getPublishedPosts($page, $perPage);
            $totalPosts = $postModel->countPublishedPosts();

            // Pagination
            $pagination = $this->paginate($totalPosts, $page, $perPage);

            // Sidebar data
            $categories = $categoryModel->getAll();
            $tags = $tagModel->getAll();
            $recentPosts = $postModel->getRecentPosts(5);

            $this->viewWithLayout('home', [
                'posts' => $posts,
                'pagination' => $pagination,
                'categories' => $categories,
                'tags' => $tags,
                'recentPosts' => $recentPosts,
                'pageTitle' => 'Trang chủ'
            ]);
        }

        //     /**
        //      * Lọc bài viết theo category
        //      * @param string $slug
        //      */
        public function byCategory($slug)
        {
            $postModel = new PostModel();
            $categoryModel = new CategoryModel();
            $tagModel = new TagModel();

            // Lấy category
            $category = $categoryModel->getBySlug($slug);
            if (!$category) {
                $this->redirect('/');
                return;
            }

            $page = $this->input('page', 1);
            $perPage = 10;

            // Lấy posts theo category
            $posts = $postModel->getByCategory($category['id'], $page, $perPage);
            $totalPosts = $postModel->countByCategory($category['id']);

            // Pagination
            $pagination = $this->paginate($totalPosts, $page, $perPage);

            // Sidebar data
            $categories = $categoryModel->getAll();
            $tags = $tagModel->getAll();
            $recentPosts = $postModel->getRecentPosts(5);

            $this->viewWithLayout('home', [
                'posts' => $posts,
                'pagination' => $pagination,
                'categories' => $categories,
                'tags' => $tags,
                'recentPosts' => $recentPosts,
                'currentCategory' => $category,
                'pageTitle' => 'Danh mục: ' . $category['name']
            ]);
        }

        //     /**
        //      * Lọc bài viết theo tag
        //      * @param string $slug
        //      */
        public function byTag($slug)
        {
            $postModel = new PostModel();
            $categoryModel = new CategoryModel();
            $tagModel = new TagModel();

            // Lấy tag
            $tag = $tagModel->getBySlug($slug);
            if (!$tag) {
                $this->redirect('/');
                return;
            }

            $page = $this->input('page', 1);
            $perPage = 10;

            // Lấy posts theo tag
            $posts = $postModel->getByTag($tag['id'], $page, $perPage);
            $totalPosts = $postModel->countByTag($tag['id']);

            // Pagination
            $pagination = $this->paginate($totalPosts, $page, $perPage);

            // Sidebar data
            $categories = $categoryModel->getAll();
            $tags = $tagModel->getAll();
            $recentPosts = $postModel->getRecentPosts(5);

            $this->viewWithLayout('home', [
                'posts' => $posts,
                'pagination' => $pagination,
                'categories' => $categories,
                'tags' => $tags,
                'recentPosts' => $recentPosts,
                'currentTag' => $tag,
                'pageTitle' => 'Tag: ' . $tag['name']
            ]);
        }

        //     /**
        //      * Tìm kiếm bài viết
        //      */
        public function search()
        {
            $keyword = $this->input('q', '');

            if (empty($keyword)) {
                $this->redirect('/');
                return;
            }

            $postModel = new PostModel();
            $categoryModel = new CategoryModel();
            $tagModel = new TagModel();

            $page = $this->input('page', 1);
            $perPage = 10;

            // Search posts
            $posts = $postModel->search($keyword, $page, $perPage);
            $totalPosts = $postModel->countSearch($keyword);

            // Pagination
            $pagination = $this->paginate($totalPosts, $page, $perPage);

            // Sidebar data
            $categories = $categoryModel->getAll();
            $tags = $tagModel->getAll();
            $recentPosts = $postModel->getRecentPosts(5);

            $this->viewWithLayout('home', [
                'posts' => $posts,
                'pagination' => $pagination,
                'categories' => $categories,
                'tags' => $tags,
                'recentPosts' => $recentPosts,
                'searchKeyword' => $keyword,
                'pageTitle' => 'Tìm kiếm: ' . $keyword
            ]);
        }
    }
