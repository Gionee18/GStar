package com.gionee.gioneeabc.bean;

import java.io.Serializable;

/**
 * Created by Linchpin25 on 1/28/2016.
 */
public class ProductBean implements Serializable {
    private int id;
    private int categoryId;
    private int imageId;
    private String productName;
    private String productDesc;
    private String productDesc1;
    private String productDesc2;
    private String productImage;
    private String productImageServerPath;
    private String productImageLocalPath;
    private String isNewProduct;
    private String vaultImageJson;
    private String productImagesJson;
    private String launch_date;
    public String getLaunch_date() {
        return launch_date;
    }

    public void setLaunch_date(String launch_date) {
        this.launch_date = launch_date;
    }




    public ProductBean() {
    }

    public String getIsNewProduct() {
        return isNewProduct;
    }

    public void setIsNewProduct(String isNewProduct) {
        this.isNewProduct = isNewProduct;
    }

    public ProductBean(int id, int categoryId, int imageId, String productName, String productDesc,String isNewProduct ,String productDesc1, String productDesc2, String productImage, String productImageServerPath, String productImageLocalPath) {
        this.id = id;
        this.categoryId = categoryId;
        this.imageId = imageId;
        this.productName = productName;
        this.productDesc = productDesc;
        this.productDesc1 = productDesc1;
        this.productDesc2 = productDesc2;
        this.productImage = productImage;
        this.productImageServerPath = productImageServerPath;
        this.productImageLocalPath = productImageLocalPath;
        this.isNewProduct=isNewProduct;

    }

    public String getProductImagesJson() {
        return productImagesJson;
    }

    public void setProductImagesJson(String productImagesJson) {
        this.productImagesJson = productImagesJson;
    }

    public String getVaultDocsJson() {
        return vaultImageJson;
    }

    public void setVaultDocsJson(String vaultImageJson) {
        this.vaultImageJson = vaultImageJson;
    }

    public int getImageId() {
        return imageId;
    }

    public void setImageId(int imageId) {
        this.imageId = imageId;
    }

    public String getProductDesc() {
        return productDesc;
    }

    public void setProductDesc(String productDesc) {
        this.productDesc = productDesc;
    }


    public String getProductImage() {
        return productImage;
    }

    public void setProductImage(String productImage) {
        this.productImage = productImage;
    }


    public String getProductDesc1() {
        return productDesc1;
    }

    public void setProductDesc1(String productDesc1) {
        this.productDesc1 = productDesc1;
    }

    public String getProductDesc2() {
        return productDesc2;
    }

    public void setProductDesc2(String productDesc2) {
        this.productDesc2 = productDesc2;
    }

    public int getCategoryId() {
        return categoryId;
    }

    public void setCategoryId(int categoryId) {
        this.categoryId = categoryId;
    }


    public int getId() {
        return id;
    }

    public void setId(int id) {
        this.id = id;
    }

    public String getProductName() {
        return productName;
    }

    public void setProductName(String productName) {
        this.productName = productName;
    }

    public String getProductImageServerPath() {
        return productImageServerPath;
    }

    public void setProductImageServerPath(String productImageServerPath) {
        this.productImageServerPath = productImageServerPath;
    }

    public String getProductImageLocalPath() {
        return productImageLocalPath;
    }

    public void setProductImageLocalPath(String productImageLocalPath) {
        this.productImageLocalPath = productImageLocalPath;
    }


}
