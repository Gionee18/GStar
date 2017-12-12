package com.gionee.gioneeabc.bean;

import java.util.ArrayList;

/**
 * Created by root on 25/10/16.
 */

public class TutorialCatogaryBean {
    public int id;
    public  String category_name;

    public  int position;
    public  int category_parent_id;
    public  String status;
    public  String description;
    public  String created_at;
    public  String updated_at;
    public ArrayList<CatImage> cat_image;

    public int getId() {
        return id;
    }

    public void setId(int id) {
        this.id = id;
    }

    public String getCategory_name() {
        return category_name;
    }

    public void setCategory_name(String category_name) {
        this.category_name = category_name;
    }

    public int getPosition() {
        return position;
    }

    public void setPosition(int position) {
        this.position = position;
    }

    public int getCategory_parent_id() {
        return category_parent_id;
    }

    public void setCategory_parent_id(int category_parent_id) {
        this.category_parent_id = category_parent_id;
    }

    public String getStatus() {
        return status;
    }

    public void setStatus(String status) {
        this.status = status;
    }

    public String getDescription() {
        return description;
    }

    public void setDescription(String description) {
        this.description = description;
    }

    public String getCreated_at() {
        return created_at;
    }

    public void setCreated_at(String created_at) {
        this.created_at = created_at;
    }

    public String getUpdated_at() {
        return updated_at;
    }

    public void setUpdated_at(String updated_at) {
        this.updated_at = updated_at;
    }

    public ArrayList<CatImage> getCat_image() {
        return cat_image;
    }

    public void setCat_image(ArrayList<CatImage> cat_image) {
        this.cat_image = cat_image;
    }

    public class CatImage {
        public int image_id;
        public String name;
        public String title;
        public String path;

        public int getImage_id() {
            return image_id;
        }

        public void setImage_id(int image_id) {
            this.image_id = image_id;
        }

        public String getName() {
            return name;
        }

        public void setName(String name) {
            this.name = name;
        }

        public String getTitle() {
            return title;
        }

        public void setTitle(String title) {
            this.title = title;
        }

        public String getPath() {
            return path;
        }

        public void setPath(String path) {
            this.path = path;
        }
    }
}
