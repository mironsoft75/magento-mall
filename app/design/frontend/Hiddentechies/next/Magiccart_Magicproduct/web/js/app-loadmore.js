define(['jquery'], function($) {
    return function (config, element) {
        const infotabs = config.infotabs;
        const $ele = $(element);
        const $loading = $ele.find('.action-loading');
        let ticking = false;
        let isEnd = false;
        let isFetching = false;

        function scrollListener(event) {
            if (!ticking && !isFetching) {
                window.requestAnimationFrame(() => {
                    if ((window.innerHeight + window.scrollY + 100) >= document.body.scrollHeight) {
                        loadmore($ele.find('.products.wrapper').data('next-page'));
                    }
                    ticking = false;
                });

                ticking = true;
            }
        }

        function loadmore(page) {
            if (!isFetching) {
                isFetching = true;
                $loading.show();
                $.ajax({
                    type: 'post',
                    data: {
                        type: config.type,
                        info: Object.assign({ p: page }, infotabs),
                        p : page,
                    },
                    url: config.url,
                    success: function(data) {
                        const $productMore = $(data);
                        const html = $productMore.find('.product-items').html();
                        const nextPage = $productMore.data('next-page');
                        console.debug(nextPage);

                        if (nextPage && html) {
                            $ele.find('.products.wrapper').data('next-page', nextPage);
                            $ele.find('.product-items').append(html);
                        } else {
                            document.removeEventListener('scroll', scrollListener);
                            $ele.find('.loadend').show();
                            isEnd = true;
                        }
                    },
                    complete: function() {
                        $loading.hide();
                        isFetching = false;
                    }
                });
            }
        }
        document.addEventListener('scroll', scrollListener);
    }
});
