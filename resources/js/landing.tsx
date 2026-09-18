import { createRoot } from 'react-dom/client';
import { Catalogue } from './landing/catalogue';
import type { CatalogueProps } from './landing/catalogue';

document
    .querySelector<HTMLFormElement>('form[data-auto-submit]')
    ?.requestSubmit();

const mount = document.getElementById('catalogue');

if (mount?.dataset.props) {
    createRoot(mount).render(
        <Catalogue {...(JSON.parse(mount.dataset.props) as CatalogueProps)} />,
    );
}
