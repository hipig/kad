import axios from 'axios';

export interface PostRecord {
    content: string;
    images: string;
}
export interface PostCommentRecord {
    content: string;
}

export function posts(params) {
    return axios.get<PostRecord[]>('posts', {params});
}

export function postComments(params) {
    return axios.get<PostCommentRecord[]>('post-comments', {params});
}
