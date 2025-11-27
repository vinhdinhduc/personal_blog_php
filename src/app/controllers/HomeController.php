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
        private $postModel;
        private $categoryModel;
        private $tagModel;


        public function __construct()
        {
            $this->postModel = new PostModel();
            $this->categoryModel = new CategoryModel();
            $this->tagModel = new TagModel();
        }

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
            $perPage = 9;

            // Lấy posts theo category
            $posts = $postModel->getByCategory($category['id'], $page, $perPage);
            $totalPosts = $postModel->countByCategory($category['id']);
            $totalPages = ceil($totalPosts / $perPage);

            // Sidebar data
            $allCategories = $categoryModel->getAll();
            $tags = $tagModel->getAll();

            $this->viewWithLayout('users/category', [
                'posts' => $posts,
                'category' => $category,
                'totalPosts' => $totalPosts,
                'totalPages' => $totalPages,
                'page' => $page,
                'allCategories' => $allCategories,
                'tags' => $tags,
                'pageTitle' => 'Danh mục: ' . $category['name']
            ], 'layouts/main');
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

        /**
         * Trang xem tất cả bài viết
         */
        public function allPosts()
        {
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $perPage = 12;
            $sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';

            // Xử lý sort
            $orderBy = 'created_at DESC';
            switch ($sort) {
                case 'oldest':
                    $orderBy = 'created_at ASC';
                    break;
                case 'most-viewed':
                    $orderBy = 'views DESC';
                    break;
                case 'title-asc':
                    $orderBy = 'title ASC';
                    break;
                case 'title-desc':
                    $orderBy = 'title DESC';
                    break;
                default:
                    $orderBy = 'created_at DESC';
            }

            $posts = $this->postModel->getPublishedPosts($page, $perPage);
            $totalPosts = $this->postModel->countPublishedPosts();
            $totalPages = ceil($totalPosts / $perPage);

            $this->viewWithLayout('users/all_posts', [
                'posts' => $posts,
                'totalPosts' => $totalPosts,
                'totalPages' => $totalPages,
                'currentPage' => $page,
                'currentSort' => $sort,
                'pageTitle' => 'Tất cả bài viết - BlogIT'
            ]);
        }

        /**
         * Trang tìm kiếm
         */
        public function search()
        {
            $keyword = isset($_GET['q']) ? trim($_GET['q']) : '';
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $perPage = 12;

            $posts = [];
            $totalPosts = 0;
            $totalPages = 0;

            if (!empty($keyword)) {
                $posts = $this->postModel->search($keyword, $page, $perPage);
                $totalPosts = $this->postModel->countSearch($keyword);
                $totalPages = ceil($totalPosts / $perPage);
            }

            $this->viewWithLayout('users/search', [
                'posts' => $posts,
                'keyword' => $keyword,
                'totalPosts' => $totalPosts,
                'totalPages' => $totalPages,
                'currentPage' => $page,
                'pageTitle' => !empty($keyword) ? "Tìm kiếm: {$keyword} - BlogIT" : 'Tìm kiếm - BlogIT'
            ]);
        }

        /**
         * Trang giới thiệu
         */
        public function about()
        {
            $this->viewWithLayout('users/about', [
                'pageTitle' => 'Giới thiệu - Blog IT'
            ], "layouts/main");
        }
    }
