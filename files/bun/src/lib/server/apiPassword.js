let apiPassword = process.env.TDA_API_PASS;

export const encodedCredentials = Buffer.from(`TdA:${apiPassword}`).toString('base64');