<?php /* Template Name: Temple Ranking Template */ ?>
<?php get_header(); ?>
<div class="sh-ranking-full">
    <main class="site-main">
        <?php while ( have_posts() ) : the_post(); the_content(); endwhile; ?>
    </main>
</div>
<?php get_footer(); ?>
