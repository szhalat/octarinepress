import throttle from "lodash.throttle";

class Navigation {
    constructor() {
        this.nav = document.querySelector('.site-header');
        this.openMobileButton = document.getElementById('toggleNav');
        this.mobileMenu = document.getElementById('mobile-menu');

        if (this.nav !== null) {
            this.topOfNav = this.nav.offsetTop;
            window.addEventListener("scroll", throttle(this.fixNav.bind(this), 10));
        }

        this.openMobileMenu();
    }

    fixNav() {
        if (window.scrollY >= 170) {
            document.body.classList.add('fixed-nav');
            document.body.style.paddingTop = this.nav.offsetHeight + 'px';
        } else {
            document.body.classList.remove('fixed-nav');
            document.body.style.paddingTop = '0';
        }
    }

    openMobileMenu() {
        if (!this.openMobileButton || !this.mobileMenu) {
            return;
        }

        const openSubmenu = document.querySelectorAll('.open-submenu');

        this.openMobileButton.addEventListener('click', () => {
            document.body.classList.toggle('mobile-opened');
            document.body.classList.toggle('overflow-hidden');
            this.mobileMenu.classList.toggle('hidden');
        });

        this.mobileMenu.querySelectorAll('a').forEach((link) => {
            link.addEventListener('click', () => {
                if (link.getAttribute('href') === '#') {
                    return;
                }

                this.closeMobileMenu();
            });
        });


        openSubmenu.forEach(item => {
            const anchor = item.previousElementSibling;

            const action = () => {
                item.nextElementSibling.classList.toggle('hidden');
                const icon = item.querySelector('svg');
                icon.classList.toggle('transform');
                icon.classList.toggle('rotate-180');
            }

            if (anchor && anchor.getAttribute('href') === '#') {

                anchor.addEventListener('click', (e) => {
                    e.preventDefault();
                    action();
                });
            }

            item.addEventListener('click', action)


        });

    }

    closeMobileMenu() {
        document.body.classList.remove('mobile-opened');
        document.body.classList.remove('overflow-hidden');
        this.mobileMenu.classList.add('hidden');
    }


}

export default Navigation;
