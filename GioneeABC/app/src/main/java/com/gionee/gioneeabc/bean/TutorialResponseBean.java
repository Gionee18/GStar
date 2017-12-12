package com.gionee.gioneeabc.bean;

import java.util.ArrayList;

/**
 * Created by root on 24/10/16.
 */
public class TutorialResponseBean {
    public int count;
    public String status;
    public ArrayList<TutorialDataCatogaryBean> data;

    public int getCount() {
        return count;
    }

    public void setCount(int count) {
        this.count = count;
    }

    public String getStatus() {
        return status;
    }

    public void setStatus(String status) {
        this.status = status;
    }

    public ArrayList<TutorialDataCatogaryBean> getData() {
        return data;
    }

    public void setData(ArrayList<TutorialDataCatogaryBean> data) {
        this.data = data;
    }

    public class TutorialDataCatogaryBean {
        public int id;
        public String category_name;

        public int position;
        public int category_parent_id;
        public String status;
        public String description;
        public String created_at;
        public String updated_at;
        public ArrayList<TutorialCatogaryCatImage> cat_image;

        public ArrayList<TutorialDataProductBean> product;

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

        public ArrayList<TutorialCatogaryCatImage> getCat_image() {
            return cat_image;
        }

        public void setCat_image(ArrayList<TutorialCatogaryCatImage> cat_image) {
            this.cat_image = cat_image;
        }


        public ArrayList<TutorialDataProductBean> getProduct() {
            return product;
        }

        public void setProduct(ArrayList<TutorialDataProductBean> product) {
            this.product = product;
        }

        public class TutorialCatogaryCatImage {
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

        public class TutorialDataCatDoc {
        }

        public class TutorialDataProductBean {
            public int id;
            public int category_id;
            public String product_name;
            public String product_desc;
            public String is_new;
            public String status;
            public String desc1;
            public String desc2;
            public String desc3;
            public int position;
            public String created_at;
            public String updated_at;
            public ArrayList<TutorialDataProductProImage> pro_image;

            public String getIs_new() {
                return is_new;
            }

            public void setIs_new(String is_new) {
                this.is_new = is_new;
            }

            public TutorialDataProductTutorials tutorials;

            public int getId() {
                return id;
            }

            public void setId(int id) {
                this.id = id;
            }

            public int getCategory_id() {
                return category_id;
            }

            public void setCategory_id(int category_id) {
                this.category_id = category_id;
            }

            public String getProduct_name() {
                return product_name;
            }

            public void setProduct_name(String product_name) {
                this.product_name = product_name;
            }

            public String getProduct_desc() {
                return product_desc;
            }

            public void setProduct_desc(String product_desc) {
                this.product_desc = product_desc;
            }

            public String getStatus() {
                return status;
            }

            public void setStatus(String status) {
                this.status = status;
            }

            public String getDesc1() {
                return desc1;
            }

            public void setDesc1(String desc1) {
                this.desc1 = desc1;
            }

            public String getDesc2() {
                return desc2;
            }

            public void setDesc2(String desc2) {
                this.desc2 = desc2;
            }

            public String getDesc3() {
                return desc3;
            }

            public void setDesc3(String desc3) {
                this.desc3 = desc3;
            }

            public int getPosition() {
                return position;
            }

            public void setPosition(int position) {
                this.position = position;
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

            public ArrayList<TutorialDataProductProImage> getPro_image() {
                return pro_image;
            }

            public void setPro_image(ArrayList<TutorialDataProductProImage> pro_image) {
                this.pro_image = pro_image;
            }


            public TutorialDataProductTutorials getTutorials() {
                return tutorials;
            }

            public void setTutorials(TutorialDataProductTutorials tutorials) {
                this.tutorials = tutorials;
            }

            public class TutorialDataProductProImage {
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

            public class TutorialDataProductProDoc {
                public int doc_id;
                public String name;
                public String title;
                public String path;

                public int getDoc_id() {
                    return doc_id;
                }

                public void setDoc_id(int doc_id) {
                    this.doc_id = doc_id;
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

            public class TutorialDataProductTutorials {
                public int video_count;
                public ArrayList<TutorialDataProductTutorialsVideo> video;

                public int getVideo_count() {
                    return video_count;
                }

                public void setVideo_count(int video_count) {
                    this.video_count = video_count;
                }

                public ArrayList<TutorialDataProductTutorialsVideo> getVideo() {
                    return video;
                }

                public void setVideo(ArrayList<TutorialDataProductTutorialsVideo> video) {
                    this.video = video;
                }

                public class TutorialDataProductTutorialsVideo {
                    public int video_id;
                    public String title;
                    public String short_description;
                    public String video_path;
                    public int category_id;
                    public int product_id;
                    public String youtube_url;
                    public String thumbnail;
                    public String duration;
                    public String status;
                    public String created_at;
                    public String updated_at;
                    private boolean isDownloaded;

                    public boolean isDownloaded() {
                        return isDownloaded;
                    }

                    public void setIsDownloaded(boolean isDownloaded) {
                        this.isDownloaded = isDownloaded;
                    }

                    public int getVideo_id() {
                        return video_id;
                    }

                    public void setVideo_id(int video_id) {
                        this.video_id = video_id;
                    }

                    public String getTitle() {
                        return title;
                    }

                    public void setTitle(String title) {
                        this.title = title;
                    }

                    public String getShort_description() {
                        return short_description;
                    }

                    public void setShort_description(String short_description) {
                        this.short_description = short_description;
                    }

                    public String getVideo_path() {
                        return video_path;
                    }

                    public void setVideo_path(String video_path) {
                        this.video_path = video_path;
                    }

                    public int getCategory_id() {
                        return category_id;
                    }

                    public void setCategory_id(int category_id) {
                        this.category_id = category_id;
                    }

                    public int getProduct_id() {
                        return product_id;
                    }

                    public void setProduct_id(int product_id) {
                        this.product_id = product_id;
                    }

                    public String getYoutube_url() {
                        return youtube_url;
                    }

                    public void setYoutube_url(String youtube_url) {
                        this.youtube_url = youtube_url;
                    }

                    public String getThumbnail() {
                        return thumbnail;
                    }

                    public void setThumbnail(String thumbnail) {
                        this.thumbnail = thumbnail;
                    }

                    public String getDuration() {
                        return duration;
                    }

                    public void setDuration(String duration) {
                        this.duration = duration;
                    }

                    public String getStatus() {
                        return status;
                    }

                    public void setStatus(String status) {
                        this.status = status;
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

                    public String getChannel_name() {
                        return channel_name;
                    }

                    public void setChannel_name(String channel_name) {
                        this.channel_name = channel_name;
                    }

                    private String channel_name;

                }
            }
        }
    }
}
