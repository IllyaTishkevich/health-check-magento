const HtmlWebpackPlugin = require('html-webpack-plugin');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
module.exports = {
    mode: 'production',
    output: {
        filename: './../../../web/js/stat/app.js'
    },
    module: {
        rules: [
            //js
            {
                test: /\.js$/,
                exclude: /node_modules/,
                use: [
                    {
                        loader: 'babel-loader',
                    }
                ]
            },
            //images
            {
                test: /\.(png|jpg|jpeg|gif|ico)$/,
                use: [
                    {
                        loader: 'file-loader',
                        options: {
                            outputPath: 'images',
                            name: '[name]-[sha1:hash:6].[ext]'
                        }
                    }
                ]
            },
            //fonts
            {
                test: /\.(ttf|otf|eot|woff|woff2)$/,
                use: [
                    {
                        loader: 'file-loader',
                        options: {
                            outputPath: 'fonts',
                            name: '[name]-[sha1:hash:6].[ext]'
                        }
                    }
                ]
            },
            //style
            {
                test: /\.css$/,
                use: [MiniCssExtractPlugin.loader,'css-loader']
            }
        ]
    },
    plugins: [
        new HtmlWebpackPlugin({
            template: 'index.html'
        }),
        new MiniCssExtractPlugin({
            filename: './../../../web/css/app.css'
        }),
    ],
    devServer: {
        open: true
    }
}