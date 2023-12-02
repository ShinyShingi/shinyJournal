// imagePlugin.js
export default {
    install(app) {
        console.log("Installing imagePlugin");
        app.config.globalProperties.$getImageUrl = (imagePath) => {
            return `${import.meta.env.VITE_API_URL}/storage/app/public/${imagePath}`;
        };
    }
};
