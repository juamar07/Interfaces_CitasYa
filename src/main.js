import { initAuth } from './store/auth.js';
import { startRouter } from './router/index.js';
import { supabase } from './config/supabaseClient.js';

await initAuth();

const ping = await supabase.rpc('get_public_negocios');
console.log('RPC OK?', ping);

startRouter();
