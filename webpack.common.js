const path = require("path");
const MiniCssExtractPlugin = require("mini-css-extract-plugin");
const { VueLoaderPlugin } = require('vue-loader');

module.exports = {
    entry: {
        app: [
            './resources/js/app.js',
            './resources/scss/app.scss'
        ],
    },
    plugins: [
        new MiniCssExtractPlugin({
            filename: 'app.css',
        }),
        new VueLoaderPlugin()
    ],
    module: {
        rules: [
            {
                test: /\.vue$/,
                loader: 'vue-loader',
                options: {
                    reactivityTransform: true
                },
            },
            {
                test: /.s?css$/,
                use: [MiniCssExtractPlugin.loader, "css-loader", "sass-loader"],
            }
        ]
    },
    output: {
        filename: "app.js",
        path: path.resolve(__dirname, "public/assets"),
    }
}