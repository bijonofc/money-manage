import Echo from "laravel-echo";
import Pusher from "pusher-js";

window.Pusher = Pusher;

const echoInstance = new Echo({
  broadcaster: "pusher",
  key: "a78bca991cad4fa1c732",
  cluster: "ap2",
  forceTLS: true,
  authEndpoint: "/broadcasting/auth",
});

export default echoInstance;
