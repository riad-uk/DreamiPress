<?php
/**
 * Template Name: Front Page
 */

get_header();
?>

<main id="primary" class="site-main">

<div class="bg-gradient-to-r from-purple-500 to-pink-500 p-8">
    <div class="container mx-auto">
        <div class="bg-white rounded-lg shadow-xl p-6 transform hover:scale-105 transition-transform duration-300">
            <h2 class="text-3xl font-bold text-gray-800 mb-4">Tailwind Test Section</h2>
            <p class="text-gray-600 mb-4">This section tests various Tailwind CSS features:</p>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-blue-100 p-4 rounded-lg hover:bg-blue-200 transition-colors">
                    <h3 class="text-blue-800 font-semibold">Colors &amp; Hover</h3>
                </div>
                <div class="bg-green-100 p-4 rounded-lg hover:bg-green-200 transition-colors">
                    <h3 class="text-green-800 font-semibold">Responsive Grid</h3>
                </div>
                <div class="bg-yellow-100 p-4 rounded-lg hover:bg-yellow-200 transition-colors">
                    <h3 class="text-yellow-800 font-semibold">Transitions</h3>
                </div>
            </div>
            <button class="mt-6 bg-indigo-500 text-white px-6 py-2 rounded-full hover:bg-indigo-600 focus:ring-2 focus:ring-indigo-300 focus:outline-none transition-colors">
                Interactive Button
            </button>
        </div>
    </div>
</div>


</main>

<?php
get_footer();