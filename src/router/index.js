import routes from './routes.js';
import { render } from './render.js';

function parseHash(){
  const raw = location.hash.slice(1) || '/';
  const [path, q] = raw.split('?');
  return { path: path.startsWith('/') ? path : `/${path}`, query: new URLSearchParams(q||'') };
}
export function navigate(to){ location.hash = to.startsWith('#') ? to : `#${to.replace(/^#/, '')}`; }

async function handle(){
  const { path, query } = parseHash();
  const route = routes.find(r => r.path === path) || routes.find(r => r.path === '/404');
  if (route?.guard) await route.guard();
  const mod = await route.component();
  const html = await mod.default({ query });
  render(html);
  if (typeof mod.onMount === 'function') mod.onMount({ query });
}
export function startRouter(){
  window.addEventListener('hashchange', handle);
  window.addEventListener('load', () => { if (!location.hash) navigate('/'); handle(); });
}
