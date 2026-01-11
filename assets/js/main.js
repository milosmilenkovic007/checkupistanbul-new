/**
 * Frontend JavaScript
 * Main entry point za frontend JS
 */
import '../scss/main.scss';

(function($) {
  'use strict';

  // Initialize modules
  const App = {
    init() {
      console.log('Hello Elementor Child Theme initialized');
      
      // Initialize components
      this.initializeModals();
      this.initializeAccordions();
      this.initializePackageAccordions();
      this.initializePackagesDetails();
      this.initializeCarousels();
      this.initializeTestimonialsNav();
      this.initializeScrollAnimations();
    },

    initializeModals() {
      // Modal functionality
      $(document).on('click', '[data-modal-toggle]', function(e) {
        e.preventDefault();
        const modalId = $(this).data('modal-toggle');
        $(`#${modalId}`).toggleClass('active');
      });

      $(document).on('click', '.modal__close', function() {
        $(this).closest('.modal').removeClass('active');
      });
    },

    initializeAccordions() {
      // Accordion functionality
      $(document).on('click', '.accordion__trigger', function() {
        const item = $(this).closest('.accordion__item');
        const isActive = item.hasClass('active');
        
        $(this).closest('.accordion').find('.accordion__item').removeClass('active');
        
        if (!isActive) {
          item.addClass('active');
        }
      });
    },

    initializePackageAccordions() {
      // Group collapse/expand: first 3 accordions work together ONLY on desktop
      const $firstRowAccordions = $('.package-sections__accordion-item:nth-child(1), .package-sections__accordion-item:nth-child(2), .package-sections__accordion-item:nth-child(3)');
      
      const isMobileOrTablet = () => window.innerWidth <= 968;
      
      $firstRowAccordions.on('toggle', function() {
        const $this = $(this);
        const isNowOpen = this.open;
        
        // Skip synchronized behavior on mobile/tablet
        if (isMobileOrTablet()) {
          return;
        }
        
        // When any is opened, open all 3 (desktop only)
        if (isNowOpen) {
          $firstRowAccordions.each(function() {
            this.open = true;
          });
        } else {
          // When any is closed, close all 3 (desktop only)
          $firstRowAccordions.each(function() {
            this.open = false;
          });
        }
      });
    },

    initializePackagesDetails() {
      const setActive = ($root, index) => {
        const idx = Number(index);
        if (Number.isNaN(idx)) return;

        // Remove previous is-active-* classes
        const classes = ($root.attr('class') || '').split(/\s+/);
        const next = classes.filter(c => !/^is-active-\d+$/.test(c));
        next.push(`is-active-${idx}`);
        $root.attr('class', next.join(' '));

        $root.find('.packages-details__tab').each(function() {
          const $btn = $(this);
          const isCurrent = Number($btn.data('index')) === idx;
          $btn.toggleClass('is-active', isCurrent);
          $btn.attr('aria-selected', isCurrent ? 'true' : 'false');
        });
      };

      const focusActiveTab = ($root) => {
        const $activeTab = $root.find('.packages-details__tab.is-active').first();
        if ($activeTab.length) {
          focusElement($activeTab);
        }
      };

      const activateIndex = ($root, index, opts = {}) => {
        const { resetState = true, resetBiochem = true, focusTab = false } = opts;
        setActive($root, index);

        if (resetState) {
          resetSectionsState($root);
        }

        // Always keep Biochemistry scroll syncing set up; only reset scroll when doing a full reset.
        setupBiochemSyncedScroll($root);
        if (resetBiochem && resetState) {
          resetBiochemScroll($root);
        }

        // Only meaningful when section states change; harmless otherwise.
        updateToggleAllLabel($root);
        markDiffItems($root);

        if (focusTab) {
          focusActiveTab($root);
        }
      };

      const normalizeItemText = (text) => {
        return String(text || '')
          .replace(/\s+/g, ' ')
          .trim()
          .toLowerCase();
      };

      const markDiffItems = ($root) => {
        const maxCols = Number($root.data('count'));
        if (!maxCols || Number.isNaN(maxCols)) return;

        const markInRow = ($row, panelSelector) => {
          // Remove any previous markings
          $row.find(`${panelSelector} .packages-details__list-item`).removeClass('pd-diff');

          for (let col = 1; col < maxCols; col += 1) {
            const $prevPanel = $row.find(`${panelSelector}[data-col="${col - 1}"]`).first();
            const $curPanel = $row.find(`${panelSelector}[data-col="${col}"]`).first();
            if (!$curPanel.length) continue;

            const prevSet = new Set();
            if ($prevPanel.length) {
              $prevPanel.find('.packages-details__list-item').each(function() {
                const $li = $(this);
                if ($li.hasClass('packages-details__list-item--empty')) return;
                const t = normalizeItemText($li.find('.packages-details__text').text());
                if (t) prevSet.add(t);
              });
            }

            $curPanel.find('.packages-details__list-item').each(function() {
              const $li = $(this);
              if ($li.hasClass('packages-details__list-item--empty')) return;
              const t = normalizeItemText($li.find('.packages-details__text').text());
              if (!t) return;
              if (!prevSet.has(t)) {
                $li.addClass('pd-diff');
              }
            });
          }
        };

        // Desktop table rows
        $root.find('.packages-details__grid-row[data-pd-row]').each(function() {
          markInRow($(this), '.packages-details__items');
        });

        // Mobile accordion details
        $root.find('details.packages-details__accordion-item').each(function() {
          markInRow($(this), '.packages-details__accordion-panel');
        });
      };

      const setupBiochemSyncedScroll = ($root) => {
        const $row = $root
          .find('.packages-details__grid-row[data-pd-section="biochemistry-laboratory"]')
          .first();
        if (!$row.length) return;

        const $lists = $row.find(
          '.packages-details__items[data-col="0"] .packages-details__list, ' +
          '.packages-details__items[data-col="1"] .packages-details__list, ' +
          '.packages-details__items[data-col="2"] .packages-details__list'
        );

        if ($lists.length < 2) return;

        // Normalize heights: pad shorter columns with invisible placeholders
        // so all columns scroll the same distance.
        const counts = [];
        $lists.each(function() {
          const $list = $(this);
          $list.find('.packages-details__list-item--empty').remove();
          counts.push($list.find('.packages-details__list-item').length);
        });

        const maxCount = Math.max.apply(null, counts);
        if (maxCount > 0) {
          $lists.each(function(i) {
            const $list = $(this);
            const missing = maxCount - counts[i];
            if (missing > 0) {
              const empties = new Array(missing)
                .fill('<li class="packages-details__list-item packages-details__list-item--empty" aria-hidden="true"></li>')
                .join('');
              $list.append(empties);
            }
          });
        }

        // Avoid stacking handlers across tab switches / re-inits
        $lists.off('scroll.pdBiochem');

        let syncing = false;
        $lists.on('scroll.pdBiochem', function(e) {
          if (syncing) return;
          syncing = true;
          const top = this.scrollTop;
          $lists.each(function() {
            if (this !== e.currentTarget) {
              this.scrollTop = top;
            }
          });
          syncing = false;
        });
      };

      const resetBiochemScroll = ($root) => {
        // Desktop lists
        $root
          .find(
            '.packages-details__grid-row[data-pd-section="biochemistry-laboratory"] ' +
              '.packages-details__items[data-col="0"] .packages-details__list, ' +
              '.packages-details__grid-row[data-pd-section="biochemistry-laboratory"] ' +
              '.packages-details__items[data-col="1"] .packages-details__list, ' +
              '.packages-details__grid-row[data-pd-section="biochemistry-laboratory"] ' +
              '.packages-details__items[data-col="2"] .packages-details__list'
          )
          .each(function() {
            this.scrollTop = 0;
          });

        // Mobile lists
        $root
          .find(
            'details.packages-details__accordion-item[data-pd-section="biochemistry-laboratory"] ' +
              '.packages-details__accordion-panel[data-col="0"] .packages-details__list, ' +
              'details.packages-details__accordion-item[data-pd-section="biochemistry-laboratory"] ' +
              '.packages-details__accordion-panel[data-col="1"] .packages-details__list, ' +
              'details.packages-details__accordion-item[data-pd-section="biochemistry-laboratory"] ' +
              '.packages-details__accordion-panel[data-col="2"] .packages-details__list'
          )
          .each(function() {
            this.scrollTop = 0;
          });
      };

      const focusElement = ($el) => {
        if (!$el || !$el.length) return;
        const node = $el[0];
        if (node && typeof node.focus === 'function') {
          try {
            node.focus({ preventScroll: true });
          } catch (e) {
            node.focus();
          }
        }
        if (node && typeof node.scrollIntoView === 'function') {
          node.scrollIntoView({ behavior: 'smooth', block: 'start', inline: 'nearest' });
        }
      };

      const updateToggleAllLabel = ($root) => {
        const $btn = $root.find('[data-pd-toggle-all]').first();
        if (!$btn.length) return;

        const $bioRow = $root.find('.packages-details__grid-row[data-pd-section="biochemistry-laboratory"]').first();
        const $bioDetails = $root.find('details.packages-details__accordion-item[data-pd-section="biochemistry-laboratory"]').first();

        const bioOpenDesktop = $bioRow.length ? !$bioRow.hasClass('is-collapsed') : false;
        const bioOpenMobile = $bioDetails.length ? $bioDetails.is('[open]') : false;
        const bioOpen = bioOpenDesktop || bioOpenMobile;

        $btn.text(bioOpen ? 'Close all' : 'Show all');
        $btn.attr('data-state', bioOpen ? 'open' : 'closed');
      };

      const openBiochemistryOnly = ($root) => {
        $root.addClass('pd-show-all');
        // Desktop: open Biochemistry row only
        const $bioRow = $root.find('.packages-details__grid-row[data-pd-section="biochemistry-laboratory"]').first();
        if ($bioRow.length) {
          $bioRow.removeClass('is-collapsed');
          const $toggle = $bioRow.find('[data-pd-row-toggle]').first();
          if ($toggle.length) {
            $toggle.attr('aria-expanded', 'true');
            focusElement($toggle);
          } else {
            focusElement($bioRow);
          }
        }

        // Mobile: open Biochemistry accordion only
        const $bioDetails = $root.find('details.packages-details__accordion-item[data-pd-section="biochemistry-laboratory"]').first();
        if ($bioDetails.length) {
          $bioDetails.attr('open', 'open');
          const $summary = $bioDetails.find('summary').first();
          focusElement($summary.length ? $summary : $bioDetails);
        }
      };

      const closeBiochemistryOnly = ($root) => {
        $root.removeClass('pd-show-all');
        const $bioRow = $root.find('.packages-details__grid-row[data-pd-section="biochemistry-laboratory"]').first();
        if ($bioRow.length) {
          $bioRow.addClass('is-collapsed');
          const $toggle = $bioRow.find('[data-pd-row-toggle]').first();
          if ($toggle.length) $toggle.attr('aria-expanded', 'false');
        }

        const $bioDetails = $root.find('details.packages-details__accordion-item[data-pd-section="biochemistry-laboratory"]').first();
        if ($bioDetails.length) {
          $bioDetails.removeAttr('open');
        }

        // Scroll to the beginning of the entire module
        const $module = $root.closest('.module-packages-details');
        if ($module.length) {
          const node = $module[0];
          if (node && typeof node.scrollIntoView === 'function') {
            node.scrollIntoView({ behavior: 'smooth', block: 'start', inline: 'nearest' });
          }
        }
      };

      const resetSectionsState = ($root) => {
        // Desktop rows
        const $rows = $root.find('.packages-details__grid-row[data-pd-row]');
        if ($rows.length) {
          const totalRows = $rows.length;
          $rows.each(function(i) {
            const $row = $(this);
            const $toggle = $row.find('[data-pd-row-toggle]').first();
            // Open all except the last one
            const shouldOpen = i < (totalRows - 1);
            $row.toggleClass('is-collapsed', !shouldOpen);
            if ($toggle.length) {
              $toggle.attr('aria-expanded', shouldOpen ? 'true' : 'false');
            }
          });
        }

        // Mobile <details>
        const $details = $root.find('.packages-details__accordion-item');
        if ($details.length) {
          const totalDetails = $details.length;
          $details.each(function(i) {
            // Open all except the last one
            if (i < (totalDetails - 1)) {
              $(this).attr('open', 'open');
            } else {
              $(this).removeAttr('open');
            }
          });
        }
      };

      // Init each module
      $('[data-packages-details]').each(function() {
        const $root = $(this);
        const $first = $root.find('.packages-details__tab').first();
        const initIdx = $first.length ? Number($first.data('index')) : 0;
        activateIndex($root, Number.isNaN(initIdx) ? 0 : initIdx, { resetState: true, resetBiochem: true, focusTab: false });
      });

      // Click handler
      $(document).on('click', '.packages-details__tab', function() {
        const $btn = $(this);
        const $root = $btn.closest('[data-packages-details]');
        if (!$root.length) return;

        // Explicit tab click keeps the existing behavior: reset open state back to the first section.
        activateIndex($root, $btn.data('index'), { resetState: true, resetBiochem: true, focusTab: true });
      });

      // Desktop: clicking anywhere in a package column switches the active package.
      // IMPORTANT: This should NOT reset the open state back to "Medical Examinations".
      $(document).on('click', '[data-packages-details] .packages-details__grid .packages-details__items', function(e) {
        if ($(e.target).closest('a, button, input, select, textarea').length) return;
        const $cell = $(this);
        const $root = $cell.closest('[data-packages-details]');
        if (!$root.length) return;

        const col = Number($cell.data('col'));
        if (Number.isNaN(col)) return;

        activateIndex($root, col, { resetState: false, resetBiochem: false, focusTab: true });
      });

      // Show all / Close all
      $(document).on('click', '[data-packages-details] [data-pd-toggle-all]', function() {
        const $btn = $(this);
        const $root = $btn.closest('[data-packages-details]');
        if (!$root.length) return;

        const state = ($btn.attr('data-state') || 'closed').toLowerCase();
        const shouldClose = state === 'open';

        if (shouldClose) {
          closeBiochemistryOnly($root);
        } else {
          openBiochemistryOnly($root);
        }

        // Keep Biochemistry behavior consistent when bulk-toggling.
        setupBiochemSyncedScroll($root);
        resetBiochemScroll($root);

        updateToggleAllLabel($root);
      });

      const toggleRow = ($root, $toggle) => {
        const $row = $toggle.closest('.packages-details__grid-row');
        if (!$row.length) return;
        const willOpen = $row.hasClass('is-collapsed');

        if (willOpen) {
          // Close all others (accordion behavior)
          $root.find('.packages-details__grid-row[data-pd-row]').each(function() {
            const $r = $(this);
            const $t = $r.find('[data-pd-row-toggle]').first();
            $r.addClass('is-collapsed');
            if ($t.length) $t.attr('aria-expanded', 'false');
          });

          // Open current
          $row.removeClass('is-collapsed');
          $toggle.attr('aria-expanded', 'true');
        } else {
          // Allow closing the current row
          $row.addClass('is-collapsed');
          $toggle.attr('aria-expanded', 'false');
        }
      };

      // Desktop: collapse/expand section rows (accordion-like)
      $(document).on('click', '[data-packages-details] .packages-details__section-title[data-pd-row-toggle]', function(e) {
        // Avoid interfering with any nested interactive elements (if introduced later)
        if ($(e.target).closest('a, button, input, select, textarea').length) return;
        const $toggle = $(this);
        const $root = $toggle.closest('[data-packages-details]');
        if (!$root.length) return;
        toggleRow($root, $toggle);

        const $row = $toggle.closest('.packages-details__grid-row');
        if (
          $row.length &&
          $row.attr('data-pd-section') === 'biochemistry-laboratory' &&
          !$row.hasClass('is-collapsed')
        ) {
          resetBiochemScroll($root);
        }
      });

      $(document).on('keydown', '[data-packages-details] .packages-details__section-title[data-pd-row-toggle]', function(e) {
        const key = e.key || e.keyCode;
        if (key === 'Enter' || key === ' ' || key === 13 || key === 32) {
          e.preventDefault();
          const $toggle = $(this);
          const $root = $toggle.closest('[data-packages-details]');
          if (!$root.length) return;
          toggleRow($root, $toggle);

          const $row = $toggle.closest('.packages-details__grid-row');
          if (
            $row.length &&
            $row.attr('data-pd-section') === 'biochemistry-laboratory' &&
            !$row.hasClass('is-collapsed')
          ) {
            resetBiochemScroll($root);
          }
        }
      });

      // Mobile: when Biochemistry <details> opens, reset list scroll position.
      $(document).on(
        'toggle',
        'details.packages-details__accordion-item[data-pd-section="biochemistry-laboratory"]',
        function() {
          if (!this.open) return;
          const $root = $(this).closest('[data-packages-details]');
          if (!$root.length) return;
          resetBiochemScroll($root);
        }
      );

      // Additional package accordion toggle
      $(document).on('click', '[data-pd-additional-toggle]', function(e) {
        if ($(e.target).closest('a, button, input, select, textarea').length) return;
        const $toggle = $(this);
        const $container = $toggle.closest('.packages-details__additional-inner');
        if (!$container.length) return;

        const isCollapsed = $container.hasClass('is-collapsed');
        $container.toggleClass('is-collapsed', !isCollapsed);
        $toggle.attr('aria-expanded', isCollapsed ? 'true' : 'false');
      });

      $(document).on('keydown', '[data-pd-additional-toggle]', function(e) {
        const key = e.key || e.keyCode;
        if (key === 'Enter' || key === ' ' || key === 13 || key === 32) {
          e.preventDefault();
          const $toggle = $(this);
          const $container = $toggle.closest('.packages-details__additional-inner');
          if (!$container.length) return;

          const isCollapsed = $container.hasClass('is-collapsed');
          $container.toggleClass('is-collapsed', !isCollapsed);
          $toggle.attr('aria-expanded', isCollapsed ? 'true' : 'false');
        }
      });
    },

  initializeTestimonialsNav() {
    const $modules = $('.module-testimonials');
    if (!$modules.length) return;

    const tryBind = ($module) => {
      const $carousel = $module.find('.testimonials__carousel').first();
      if (!$carousel.length) return false;

      const $btnPrev = $module.find('.testimonials__nav-btn--prev').first();
      const $btnNext = $module.find('.testimonials__nav-btn--next').first();
      if (!$btnPrev.length || !$btnNext.length) return false;

      // 1) Swiper (most Trustindex widgets)
      const swiperEl = $carousel[0].querySelector('.swiper, .swiper-container');
      const swiper = swiperEl && swiperEl.swiper ? swiperEl.swiper : null;
      if (swiper) {
        // Remove the "peek" effect by disabling centered slides and forcing exact slides per view.
        try {
          swiper.params.centeredSlides = false;
          swiper.params.slidesPerView = 3;
          swiper.params.spaceBetween = 28;
          swiper.params.slidesOffsetBefore = 0;
          swiper.params.slidesOffsetAfter = 0;
          swiper.params.breakpoints = Object.assign({}, swiper.params.breakpoints || {}, {
            0: { slidesPerView: 1, spaceBetween: 16, centeredSlides: false },
            768: { slidesPerView: 2, spaceBetween: 20, centeredSlides: false },
            992: { slidesPerView: 3, spaceBetween: 28, centeredSlides: false },
          });
          swiper.update();
        } catch (e) {}

        $btnPrev.off('click.testimonialsNav').on('click.testimonialsNav', function() {
          swiper.slidePrev();
        });
        $btnNext.off('click.testimonialsNav').on('click.testimonialsNav', function() {
          swiper.slideNext();
        });
        return true;
      }

      // 2) Slick
      const $slick = $carousel.find('.slick-slider.slick-initialized, .slick-initialized').first();
      if ($slick.length && typeof $slick.slick === 'function') {
        try {
          $slick.slick('slickSetOption', {
            centerMode: false,
            slidesToShow: 3,
            slidesToScroll: 1,
          }, true);
        } catch (e) {}

        $btnPrev.off('click.testimonialsNav').on('click.testimonialsNav', function() {
          $slick.slick('slickPrev');
        });
        $btnNext.off('click.testimonialsNav').on('click.testimonialsNav', function() {
          $slick.slick('slickNext');
        });
        return true;
      }

      // 3) Fallback: click Trustindex-provided arrows (hidden via CSS)
      const btnPrev = $carousel[0].querySelector('.ti-prev, .swiper-button-prev, .slick-prev');
      const btnNext = $carousel[0].querySelector('.ti-next, .swiper-button-next, .slick-next');
      if (btnPrev || btnNext) {
        $btnPrev.off('click.testimonialsNav').on('click.testimonialsNav', function() {
          if (btnPrev) btnPrev.dispatchEvent(new MouseEvent('click', { bubbles: true }));
        });
        $btnNext.off('click.testimonialsNav').on('click.testimonialsNav', function() {
          if (btnNext) btnNext.dispatchEvent(new MouseEvent('click', { bubbles: true }));
        });
        return true;
      }

      return false;
    };

    $modules.each(function() {
      const $module = $(this);
      let attempts = 0;
      const timer = setInterval(() => {
        attempts += 1;
        if (tryBind($module) || attempts >= 25) {
          clearInterval(timer);
        }
      }, 200);
    });
  },

    initializeCarousels() {
      // Carousel functionality (if using a carousel library)
      $('.carousel').each(function() {
        // Initialize carousel
      });
    },

    initializeScrollAnimations() {
      // Observe elements for scroll animations
      if ('IntersectionObserver' in window) {
        const observer = new IntersectionObserver((entries) => {
          entries.forEach(entry => {
            if (entry.isIntersecting) {
              entry.target.classList.add('animate');
              observer.unobserve(entry.target);
            }
          });
        }, {
          threshold: 0.1
        });

        document.querySelectorAll('[data-animate]').forEach(el => {
          observer.observe(el);
        });
      }
    }
  };

  $(document).ready(() => App.init());

})(jQuery);
