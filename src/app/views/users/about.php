<!-- File: views/about.php -->

<!-- Hero Section -->
<section class="about-hero">
    <div class="about-hero__container">
        <h1 class="about-hero__title">
            Về chúng tôi
        </h1>
        <p class="about-hero__subtitle">
            Nơi chia sẻ kiến thức và đam mê công nghệ
        </p>
        <p class="about-hero__description">
            Chúng tôi là một cộng đồng những người yêu thích công nghệ, luôn khao khát học hỏi và chia sẻ kiến thức.
            Blog của chúng tôi ra đời với sứ mệnh mang đến những bài viết chất lượng, hữu ích và dễ hiểu về
            lập trình, công nghệ và những xu hướng mới trong ngành IT.
        </p>
    </div>
</section>

<!-- Story Section -->
<section class="about-content">
    <div class="about-content__container">

        <!-- Our Story -->
        <div class="about-content__grid">
            <div class="about-content__image">
                <img src="<?= Router::url('assets/images/team/image.png') ?>"


                    alt="Câu chuyện của chúng tôi"
                    loading="lazy">
            </div>
            <div class="about-content__text">
                <h2 class="about-content__heading">
                    Câu chuyện của chúng tôi
                </h2>
                <p class="about-content__paragraph">
                    Được thành lập vào năm 2020, blog của chúng tôi bắt đầu từ một ý tưởng đơn giản:
                    tạo ra một nơi mà mọi người có thể dễ dàng tiếp cận với kiến thức lập trình
                    một cách miễn phí và chất lượng.
                </p>
                <p class="about-content__paragraph">
                    Từ những ngày đầu với vài bài viết cơ bản, chúng tôi đã phát triển thành một
                    cộng đồng với hàng nghìn người theo dõi, chia sẻ hàng trăm bài viết về đa dạng
                    các chủ đề từ web development, mobile apps đến AI và Machine Learning.
                </p>
                <p class="about-content__paragraph">
                    Mỗi ngày, chúng tôi tiếp tục nỗ lực để mang đến những nội dung hữu ích nhất,
                    giúp cộng đồng developer Việt Nam ngày càng phát triển mạnh mẽ hơn.
                </p>
            </div>
        </div>

        <!-- Our Mission -->
        <div class="about-content__grid about-content__grid--reverse">
            <div class="about-content__image">
                <img src="<?= Router::url('assets/images/team/image.png') ?>"

                    alt="Sứ mệnh của chúng tôi"
                    loading="lazy">
            </div>
            <div class="about-content__text">
                <h2 class="about-content__heading">
                    Sứ mệnh của chúng tôi
                </h2>
                <p class="about-content__paragraph">
                    <strong>Sứ mệnh</strong> của chúng tôi là làm cho lập trình trở nên dễ tiếp cận
                    hơn với mọi người, bất kể bạn là người mới bắt đầu hay đã có kinh nghiệm.
                </p>
                <p class="about-content__paragraph">
                    Chúng tôi tin rằng kiến thức nên được chia sẻ tự do và mọi người đều có quyền
                    học hỏi và phát triển kỹ năng của mình. Vì vậy, tất cả nội dung trên blog
                    đều hoàn toàn miễn phí.
                </p>
                <p class="about-content__paragraph">
                    <strong>Tầm nhìn</strong> của chúng tôi là trở thành nguồn tài liệu lập trình
                    hàng đầu tại Việt Nam, nơi mà mọi developer đều có thể tìm thấy câu trả lời
                    cho những thắc mắc của mình.
                </p>
            </div>
        </div>

    </div>
</section>

<!-- Stats Section -->
<section class="about-stats">
    <div class="about-stats__container">
        <h2 class="about-stats__title">Con số ấn tượng</h2>
        <div class="about-stats__grid">

            <div class="stat-card">
                <div class="stat-card__icon">
                    <i class="fas fa-newspaper"></i>
                </div>
                <div class="stat-card__number" data-count="1234">0</div>
                <div class="stat-card__label">Bài viết</div>
            </div>

            <div class="stat-card">
                <div class="stat-card__icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-card__number" data-count="50000">0</div>
                <div class="stat-card__label">Độc giả</div>
            </div>

            <div class="stat-card">
                <div class="stat-card__icon">
                    <i class="fas fa-comments"></i>
                </div>
                <div class="stat-card__number" data-count="12500">0</div>
                <div class="stat-card__label">Bình luận</div>
            </div>

            <div class="stat-card">
                <div class="stat-card__icon">
                    <i class="fas fa-award"></i>
                </div>
                <div class="stat-card__number" data-count="150">0</div>
                <div class="stat-card__label">Tutorials</div>
            </div>

        </div>
    </div>
</section>

<!-- Team Section -->
<section class="about-team">
    <div class="about-team__container">
        <h2 class="about-team__title">Đội ngũ của chúng tôi</h2>
        <p class="about-team__subtitle">
            Những người đam mê công nghệ và sẵn sàng chia sẻ kiến thức
        </p>

        <div class="about-team__grid">

            <!-- Team Member 1 -->
            <div class="team-card">
                <div class="team-card__image">
                    <img src="<?= Router::url('assets/images/team/member1.png') ?>"
                        alt="Cầm Văn Biên"
                        loading="lazy">
                    <div class="team-card__overlay">
                        <div class="team-card__social">
                            <a href="#" class="team-card__social-link" aria-label="Facebook">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            <a href="#" class="team-card__social-link" aria-label="Twitter">
                                <i class="fab fa-twitter"></i>
                            </a>
                            <a href="#" class="team-card__social-link" aria-label="LinkedIn">
                                <i class="fab fa-linkedin-in"></i>
                            </a>
                            <a href="#" class="team-card__social-link" aria-label="GitHub">
                                <i class="fab fa-github"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="team-card__content">
                    <h3 class="team-card__name">Cầm Văn Biên</h3>
                    <div class="team-card__role">Developer</div>
                    <p class="team-card__bio">

                        Đam mê chia sẻ kiến thức và xây dựng cộng đồng developer.
                    </p>
                </div>
            </div>

            <!-- Team Member 2 -->
            <div class="team-card">
                <div class="team-card__image">
                    <img src="<?= Router::url('assets/images/team/member2.jpg') ?>"
                        alt="Đinh Đức Vình"
                        loading="lazy">
                    <div class="team-card__overlay">
                        <div class="team-card__social">
                            <a href="#" class="team-card__social-link" aria-label="Facebook">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            <a href="#" class="team-card__social-link" aria-label="Twitter">
                                <i class="fab fa-twitter"></i>
                            </a>
                            <a href="#" class="team-card__social-link" aria-label="LinkedIn">
                                <i class="fab fa-linkedin-in"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="team-card__content">
                    <h3 class="team-card__name">Đinh Đức Vình</h3>
                    <div class="team-card__role">Full-stack Developer</div>
                    <p class="team-card__bio">
                        Chuyên gia về Full-stack Development và Cloud Architecture.
                        Yêu thích viết bài về React, Node.js và DevOps.
                    </p>
                </div>
            </div>

            <!-- Team Member 3 -->
            <div class="team-card">
                <div class="team-card__image">
                    <img src="<?= Router::url('assets/images/team/member3.png') ?>"
                        alt="Mùa A Chự"
                        loading="lazy">
                    <div class="team-card__overlay">
                        <div class="team-card__social">
                            <a href="#" class="team-card__social-link" aria-label="Facebook">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            <a href="#" class="team-card__social-link" aria-label="LinkedIn">
                                <i class="fab fa-linkedin-in"></i>
                            </a>
                            <a href="#" class="team-card__social-link" aria-label="GitHub">
                                <i class="fab fa-github"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="team-card__content">
                    <h3 class="team-card__name">Mùa A Chự</h3>
                    <div class="team-card__role">Content Creator</div>
                    <p class="team-card__bio">
                        Chuyên viết các tutorial về Python, AI và Machine Learning.
                        Có niềm đam mê với việc giải thích những khái niệm phức tạp một cách đơn giản.
                    </p>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- Values Section -->
<section class="about-values">
    <div class="about-values__container">
        <h2 class="about-values__title">Giá trị cốt lõi</h2>

        <div class="about-values__grid">

            <div class="value-card">
                <div class="value-card__icon">
                    <i class="fas fa-heart"></i>
                </div>
                <h3 class="value-card__title">Đam mê</h3>
                <p class="value-card__description">
                    Chúng tôi yêu thích công nghệ và luôn nhiệt huyết trong việc học hỏi,
                    khám phá những điều mới mẻ để chia sẻ với cộng đồng.
                </p>
            </div>

            <div class="value-card">
                <div class="value-card__icon">
                    <i class="fas fa-lightbulb"></i>
                </div>
                <h3 class="value-card__title">Sáng tạo</h3>
                <p class="value-card__description">
                    Chúng tôi luôn tìm kiếm những cách tiếp cận mới, độc đáo để làm cho
                    nội dung trở nên hấp dẫn và dễ hiểu hơn.
                </p>
            </div>

            <div class="value-card">
                <div class="value-card__icon">
                    <i class="fas fa-users"></i>
                </div>
                <h3 class="value-card__title">Cộng đồng</h3>
                <p class="value-card__description">
                    Chúng tôi xây dựng một cộng đồng nơi mọi người có thể học hỏi,
                    chia sẻ và phát triển cùng nhau.
                </p>
            </div>

            <div class="value-card">
                <div class="value-card__icon">
                    <i class="fas fa-star"></i>
                </div>
                <h3 class="value-card__title">Chất lượng</h3>
                <p class="value-card__description">
                    Mỗi bài viết đều được nghiên cứu kỹ lưỡng, kiểm tra kỹ càng để
                    đảm bảo mang đến giá trị tốt nhất cho độc giả.
                </p>
            </div>

            <div class="value-card">
                <div class="value-card__icon">
                    <i class="fas fa-gift"></i>
                </div>
                <h3 class="value-card__title">Chia sẻ</h3>
                <p class="value-card__description">
                    Tất cả kiến thức đều được chia sẻ miễn phí. Chúng tôi tin rằng
                    giáo dục nên được tiếp cận bởi tất cả mọi người.
                </p>
            </div>

            <div class="value-card">
                <div class="value-card__icon">
                    <i class="fas fa-rocket"></i>
                </div>
                <h3 class="value-card__title">Đổi mới</h3>
                <p class="value-card__description">
                    Chúng tôi luôn cập nhật những xu hướng công nghệ mới nhất và
                    không ngừng cải thiện để mang đến trải nghiệm tốt nhất.
                </p>
            </div>

        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="about-cta">
    <div class="about-cta__container">
        <h2 class="about-cta__title">Sẵn sàng bắt đầu hành trình?</h2>
        <p class="about-cta__description">
            Tham gia cùng hàng nghìn developer đang học hỏi và phát triển mỗi ngày.
            Đăng ký nhận bài viết mới nhất hoặc liên hệ với chúng tôi ngay hôm nay!
        </p>
        <div class="about-cta__buttons">
            <a href="/register" class="about-cta__button about-cta__button--primary">
                <i class="fas fa-user-plus"></i>
                Đăng ký ngay
            </a>
            <a href="/contact" class="about-cta__button about-cta__button--secondary">
                <i class="fas fa-envelope"></i>
                Liên hệ với chúng tôi
            </a>
        </div>
    </div>
</section>

<!-- Counter Animation Script -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Animate counters when visible
        const counters = document.querySelectorAll('.stat-card__number');

        const animateCounter = (counter) => {
            const target = parseInt(counter.getAttribute('data-count'));
            const duration = 2000;
            const step = target / (duration / 16);
            let current = 0;

            const updateCounter = () => {
                current += step;
                if (current < target) {
                    counter.textContent = Math.floor(current).toLocaleString('vi-VN');
                    requestAnimationFrame(updateCounter);
                } else {
                    counter.textContent = target.toLocaleString('vi-VN');
                }
            };

            updateCounter();
        };

        // Intersection Observer for animation trigger
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    animateCounter(entry.target);
                    observer.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.5
        });

        counters.forEach(counter => observer.observe(counter));
    });
</script>