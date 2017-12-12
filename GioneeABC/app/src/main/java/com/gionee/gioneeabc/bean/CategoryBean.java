package com.gionee.gioneeabc.bean;

/**
 * Created by Linchpin25 on 3/1/2016.
 */
public class CategoryBean {

    private int categoryId;
    private String categoryName;
    private int categoryParentId;
    private String categoryDesc;
    private String categoryImage;
    private int imageId;
    private String imageServerPath;
    private String imageLocalPath;
    private int position;


    public CategoryBean() {
    }

    public CategoryBean(int categoryId, String categoryName,int categoryParentId,String categoryDesc,String categoryImage, int imageId, String imageServerPath, String imageLocalPath,int position) {
        this.categoryId = categoryId;
        this.categoryName = categoryName;
        this.categoryParentId=categoryParentId;
        this.categoryDesc=categoryDesc;
        this.categoryImage = categoryImage;
        this.imageId = imageId;
        this.imageServerPath = imageServerPath;
        this.imageLocalPath = imageLocalPath;
        this.position=position;
    }


    public int getCategoryPosition() {
        return position;
    }

    public void setCategoryPosition(int position) {
        this.position = position;
    }

    public int getImageId() {
        return imageId;
    }

    public void setImageId(int imageId) {
        this.imageId = imageId;
    }

    public String getImageServerPath() {
        return imageServerPath;
    }

    public void setImageServerPath(String imageServerPath) {
        this.imageServerPath = imageServerPath;
    }

    public String getImageLocalPath() {
        return imageLocalPath;
    }

    public void setImageLocalPath(String imageLocalPath) {
        this.imageLocalPath = imageLocalPath;
    }

    public int getCategoryParentId() {
        return categoryParentId;
    }

    public void setCategoryParentId(int categoryParentId) {
        this.categoryParentId = categoryParentId;
    }

    public String getCategoryDesc() {
        return categoryDesc;
    }

    public void setCategoryDesc(String categoryDesc) {
        this.categoryDesc = categoryDesc;
    }


    public String getCategoryImage() {
        return categoryImage;
    }

    public void setCategoryImage(String categoryImage) {
        this.categoryImage = categoryImage;
    }



    public int getCategoryId() {
        return categoryId;
    }

    public void setCategoryId(int categoryId) {
        this.categoryId = categoryId;
    }

    public String getCategoryName() {
        return categoryName;
    }

    public void setCategoryName(String categoryName) {
        this.categoryName = categoryName;
    }
}
