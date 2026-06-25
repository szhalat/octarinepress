import { gsap } from "gsap";
import { SplitText } from "gsap/SplitText";
import { ScrollTrigger } from "gsap/ScrollTrigger";

export default class Animations {
    constructor() {
        this.context = null;
        this.media = null;
        this.reducedMotionQuery = window.matchMedia("(prefers-reduced-motion: reduce)");
    }

    init() {
        if (!document.body) {
            return;
        }

        gsap.registerPlugin(ScrollTrigger, SplitText);

        if (this.reducedMotionQuery.matches) {
            return;
        }

        this.context = gsap.context(() => {
            this.media = gsap.matchMedia();

            this.setupSplitHeadings();
            this.setupCustomHeaders();
            this.setupColumnParagraphs();
            this.setupStaggers();
            this.setupElements();
            this.setupParallax();

            ScrollTrigger.refresh();
        }, document.body);
    }

    setupSplitHeadings() {
        gsap.utils.toArray("[data-animate='heading']").forEach((heading) => {
            this.animateSplitHeading(heading);
        });
    }

    setupCustomHeaders() {
        gsap.utils.toArray(".custom-header:not([data-animate='heading'])").forEach((heading) => {
            this.animateSplitHeading(heading);
        });
    }

    setupColumnParagraphs() {
        gsap.utils.toArray(".two-columns-block__column").forEach((column) => {
            const paragraphs = gsap.utils.toArray(column.querySelectorAll("p"));

            if (paragraphs.length === 0) {
                return;
            }

            this.revealItems(paragraphs, {
                trigger: column,
                start: "top 85%",
                y: 20,
                duration: 0.7,
                stagger: 0.1,
            });
        });
    }

    setupStaggers() {
        gsap.utils.toArray("[data-animate='stagger']").forEach((container) => {
            const items = gsap
                .utils
                .toArray(container.querySelectorAll("[data-animate-item]"))
                .filter((item) => {
                    if (item.classList.contains("is-clone")) {
                        return false;
                    }

                    return item.closest("[data-animate='stagger']") === container;
                });

            if (items.length === 0) {
                return;
            }

            this.revealItems(items, this.getRevealOptions(container, {
                trigger: container,
                start: "top 82%",
                y: 24,
                duration: 0.85,
                stagger: 0.1,
                scale: 1,
            }));
        });
    }

    setupElements() {
        gsap.utils.toArray("[data-animate]").forEach((element) => {
            const type = element.dataset.animate;

            if (!type || type === "heading" || type === "stagger" || type === "parallax") {
                return;
            }

            const preset = this.getPreset(type);

            this.revealItems([element], this.getRevealOptions(element, preset));
        });
    }

    setupParallax() {
        this.media.add("(min-width: 1024px)", () => {
            gsap.utils.toArray("[data-animate='parallax']").forEach((element) => {
                const trigger = element.closest("[data-animate-parallax-wrap]") || element.parentElement;

                if (!trigger) {
                    return;
                }

                gsap.fromTo(
                    element,
                    {
                        yPercent: this.getNumberAttr(element, "data-animate-from-y-percent", -4),
                        scale: this.getNumberAttr(element, "data-animate-from-scale", 1.03),
                    },
                    {
                        yPercent: this.getNumberAttr(element, "data-animate-to-y-percent", 4),
                        scale: this.getNumberAttr(element, "data-animate-to-scale", 1.07),
                        ease: "none",
                        scrollTrigger: {
                            trigger,
                            start: element.dataset.animateStart || "top bottom",
                            end: element.dataset.animateEnd || "bottom top",
                            scrub: true,
                        },
                    },
                );
            });
        });
    }

    animateSplitHeading(heading) {
        if (!heading || !heading.textContent.trim()) {
            return;
        }

        try {
            const split = new SplitText(heading, {
                type: "lines,words",
                linesClass: "split-line",
            });
            const targets = split.words.length > 0 ? split.words : split.lines;

            gsap.set(split.lines, { overflow: "hidden" });
            gsap.set(targets, { autoAlpha: 0, yPercent: 110 });

            const animation = {
                autoAlpha: 1,
                yPercent: 0,
                duration: this.getNumberAttr(heading, "data-animate-duration", this.getBoolAttr(heading, "data-animate-immediate") ? 0.95 : 0.85),
                stagger: this.getNumberAttr(heading, "data-animate-stagger", split.words.length > 0 ? 0.018 : 0.08),
                ease: "power3.out",
                clearProps: "opacity,visibility,transform",
                onComplete: () => split.revert(),
            };

            if (this.getBoolAttr(heading, "data-animate-immediate")) {
                gsap.to(targets, {
                    ...animation,
                    delay: this.getNumberAttr(heading, "data-animate-delay", 0.05),
                });
                return;
            }

            gsap.to(targets, {
                ...animation,
                scrollTrigger: {
                    trigger: heading,
                    start: heading.dataset.animateStart || "top 85%",
                    once: true,
                },
            });
        } catch (error) {
            this.revealItems([heading], this.getRevealOptions(heading, {
                trigger: heading,
                start: "top 85%",
                y: 26,
                duration: 0.8,
            }));
        }
    }

    revealItems(items, options = {}) {
        const targets = items.filter(Boolean);

        if (targets.length === 0) {
            return;
        }

        const fromVars = {
            autoAlpha: 0,
            y: options.y ?? 24,
        };

        if (options.scale && options.scale !== 1) {
            fromVars.scale = options.scale;
            fromVars.transformOrigin = "50% 100%";
        }

        const toVars = {
            autoAlpha: 1,
            y: 0,
            scale: 1,
            duration: options.duration ?? 0.75,
            stagger: options.stagger ?? 0,
            delay: options.delay ?? 0,
            ease: options.ease || "power2.out",
            clearProps: "opacity,visibility,transform",
            overwrite: "auto",
        };

        if (options.immediate) {
            gsap.fromTo(targets, fromVars, toVars);
            return;
        }

        gsap.fromTo(targets, fromVars, {
            ...toVars,
            scrollTrigger: options.trigger
                ? {
                    trigger: options.trigger,
                    start: options.start || "top 86%",
                    once: true,
                }
                : undefined,
        });
    }

    getPreset(type) {
        switch (type) {
            case "fade-down":
                return {
                    trigger: null,
                    start: "top 88%",
                    y: -16,
                    duration: 0.7,
                    scale: 1,
                };
            case "zoom-in":
                return {
                    trigger: null,
                    start: "top 88%",
                    y: 28,
                    duration: 0.95,
                    scale: 0.985,
                };
            case "fade-up":
            default:
                return {
                    trigger: null,
                    start: "top 88%",
                    y: 24,
                    duration: 0.8,
                    scale: 1,
                };
        }
    }

    getRevealOptions(element, defaults = {}) {
        return {
            trigger: this.getBoolAttr(element, "data-animate-immediate")
                ? null
                : defaults.trigger || element,
            immediate: this.getBoolAttr(element, "data-animate-immediate"),
            start: element.dataset.animateStart || defaults.start,
            y: this.getNumberAttr(element, "data-animate-y", defaults.y ?? 24),
            duration: this.getNumberAttr(element, "data-animate-duration", defaults.duration ?? 0.8),
            delay: this.getNumberAttr(element, "data-animate-delay", defaults.delay ?? 0),
            stagger: this.getNumberAttr(element, "data-animate-stagger", defaults.stagger ?? 0),
            scale: this.getNumberAttr(element, "data-animate-scale", defaults.scale ?? 1),
            ease: element.dataset.animateEase || defaults.ease || "power2.out",
        };
    }

    getBoolAttr(element, attribute) {
        return element.getAttribute(attribute) === "true";
    }

    getNumberAttr(element, attribute, fallback) {
        const value = element.getAttribute(attribute);

        if (value === null || value === "") {
            return fallback;
        }

        const parsed = Number(value);
        return Number.isFinite(parsed) ? parsed : fallback;
    }
}
